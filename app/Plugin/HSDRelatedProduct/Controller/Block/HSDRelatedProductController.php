<?php

namespace Plugin\HSDRelatedProduct\Controller\Block;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Plugin\HSDRelatedProduct\Entity\HSDRelatedProduct;


class HSDRelatedProductController
{
    //タイトル
    private $_title = 'この商品をみた人はこんな商品もみています';

    // 表示個数
    private $_show_count = 4; //初期値4

    // 関連商品用データ
    private $_rp = null;

    // 価格の表示/非表示
    private $_show_price = null;

    /**
     * HSDRelatedProduct画面
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        $id = $app['request']->attributes->get('id');

        /*
         * もしセッションにsave_pr_idが保持されていたら処理を行う
         */
        if( isset($_SESSION['ec_save_pr_id']) ){

            // 保持する最大データ数を取得し、1回の削除数を設定（20%を設定）
            $setting = $app['hsd_related_product_setting.repository.hsd_related_product_setting']
                ->findOneBy(array('id' => '1'));
            $max_row = $setting['max_row_num'];
            $sc = $setting['max_num'];
            $title = $setting['title'];
            $this->show_price = $setting['show_price'];
            if( !empty($max_row) && is_numeric($max_row) ) {
                $del_rows = intval($max_row * 0.2);
            }else{
                $max_row = 1000; // 初期値1000
                $del_rows = 200;
            }
            if( !empty($sc) && is_numeric($sc) ) {
                $this->_show_count = $sc;
            }
            if( !empty($title) ) {
                $this->_title = $title;
            }

            // データ保持数に達していたら削除
            $query = $app['orm.em']
                ->createQuery(
                    'SELECT count(rp.id) cn FROM Plugin\HSDRelatedProduct\Entity\HSDRelatedProduct rp'
                );
            $rs = $query->getResult();
            if($rs[0]['cn'] > $max_row){
                // 古いrowを削除
                if($rs[0]['cn'] > $max_row){
                    $del_rows = $rs[0]['cn'] - $max_row;
                }
                $stmt = $app['orm.em']->getConnection()->prepare('delete from plg_hsd_related_product order by updated_at asc limit ' . $del_rows);
                $stmt->execute();
            }

            $_from_id = $_SESSION['ec_save_pr_id'];

            // DB更新：もしfromとtoが異なる場合は保持
            if($_from_id != $id){
                $rp_obj = new HSDRelatedProduct();
                $rp_obj->setId(uniqid('rp_'))
                    ->setFromId($_from_id)
                    ->setToId($id)
                    ->setUpdatedAt(date('Y-m-d H:i:s'));

                $app['orm.em']->persist($rp_obj);
                $app['orm.em']->flush($rp_obj);
            }

            // 現在の商品IDをもとに、次の商品IDを取得
            $stmt = $app['orm.em']->getConnection()->prepare('
                    SELECT count(rp.to_id) cn, rp.to_id FROM plg_hsd_related_product rp, dtb_product as p WHERE rp.from_id=' . $id . ' AND rp.to_id = p.product_id AND p.del_flg = 0 AND p.status = 1 GROUP BY rp.from_id, rp.to_id ORDER BY cn DESC
                    ');
            $stmt->execute();
            $rs = $stmt->fetchAll();

            // 関連商品自動表示ブロックの設定
            $or_str = '';
            foreach($rs as $item){
                $or_str .= '(ecp.product_id=' . $item['to_id'] . ' AND ecp.product_id = ecpi.product_id AND ecpi.rank=1) or ';
            }
            $or_str = substr($or_str, 0, strlen($or_str)-4);

            $sql =
<<<SQL
SELECT ecp.product_id, ecp.name, ecp.description_detail, ecpi.file_name
, (select MIN(in_pcl.price02) FROM dtb_product_class in_pcl 
    WHERE in_pcl.product_id = ecp.product_id 
    GROUP BY in_pcl.product_id
    ) min_price
, (select MAX(in_pcl.price02) FROM dtb_product_class in_pcl 
    WHERE in_pcl.product_id = ecp.product_id 
    GROUP BY in_pcl.product_id
    ) max_price 
,opm.maker_id maker_id
,(select name from plg_maker pmk where pmk.maker_id=opm.maker_id)maker_name
,(select MIN(in_pcl.product_type_id) from dtb_product_class in_pcl
  WHERE in_pcl.product_id = ecp.product_id  AND in_pcl.del_flg <> 1 
  and in_pcl.class_category_id1 > 0
  and in_pcl.class_category_id2 > 0
  GROUP BY in_pcl.product_id
) product_type_id
,(select MIN(in_pcl.price02) FROM dtb_product_class in_pcl
 inner join dtb_category in_cl
 on in_cl.category_id=in_pcl.class_category_id2
 and in_cl.category_id=9
  WHERE in_pcl.product_id = ecp.product_id AND in_pcl.del_flg <> 1 
  GROUP BY in_pcl.product_id) min_price
,(select MAX(in_pcl.price02) FROM dtb_product_class in_pcl
 inner join dtb_category in_cl
 on in_cl.category_id=in_pcl.class_category_id2
 and in_cl.category_id=10
   WHERE in_pcl.product_id = ecp.product_id AND in_pcl.del_flg <> 1
    GROUP BY in_pcl.product_id) max_price 
,(select MAX(in_pcl.price02) FROM dtb_product_class in_pcl
 inner join dtb_category in_cl
 on in_cl.category_id=in_pcl.class_category_id2
 and in_cl.category_id=12
   WHERE in_pcl.product_id = ecp.product_id AND in_pcl.del_flg <> 1
    GROUP BY in_pcl.product_id) min_price_pm
FROM dtb_product ecp
inner join plg_product_maker opm on ecp.product_id = opm.product_id 
inner join dtb_product_image ecpi on ecp.product_id = ecpi.product_id AND ecpi.rank=1 
SQL;

            if(strlen($or_str) > 1) {
                $stmt = $app['orm.em']->getConnection()->prepare(
                    $sql
                    .' WHERE '. $or_str );
                $stmt->execute();
                $this->_rp = $stmt->fetchAll();
            }

        }

        /*
         * 現在の商品id をセッションに保持
         */
        $_SESSION['ec_save_pr_id'] = $id;

        return $app['view']->render("Block/hsd_related_product.twig", array(
            'title' => $this->_title,
            'max_count' => $this->_show_count,
            'rp_count' => count($this->_rp),
            'hsd_related_product' => $this->_rp,
            'show_price' => $this->show_price
        ));

    }

}
