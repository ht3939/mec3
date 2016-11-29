<?php

namespace Plugin\HSDSameCategory\Controller\Block;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Plugin\HSDSameCategory\Entity\HSDSameCategory;


class HSDSameCategoryController
{
    //タイトル
    private $_title = '同じカテゴリの商品';

    // 表示個数
    private $_show_count = 4; //初期値4

    // 関連商品用データ
    private $_rp = null;

    // 表示タイプ
    private $_show_type = 'normal'; //初期値 スライダーなし

    // ページネーション
    private $_pagination = 'true'; //初期値 あり

    // ナビゲーション
    private $_navbuttons = 'true'; //初期値 あり

    // 表示の自動ループ
    private $_showloop = 'true'; //初期値 あり

    /**
     * HSDSameCategory画面
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        $id = $app['request']->attributes->get('id');

        // セッティングを読み込み
        $setting = $app['hsd_same_category_setting.repository.hsd_same_category_setting']
            ->findOneBy(array('id' => '1'));
        $max_count = $setting['max_count'];
        $title = $setting['title'];
        if( !empty($max_count) && is_numeric($max_count) ) {
            $this->_show_count = $max_count;
        }
        if( !empty($title) ){
            $this->_title = $title;
        }
        if( !empty($setting['show_type']) ){
            $this->_show_type = $setting['show_type'];
        }
        if( !empty($setting['pagination']) ){
            $this->_pagination = $setting['pagination'];
        }
        if( !empty($setting['navbuttons']) ){
            $this->_navbuttons = $setting['navbuttons'];
        }
        if( !empty($setting['showloop']) ){
            $this->_showloop = $setting['showloop'];
        }

        /*
         * 同じカテゴリのproduct_idを取得
         */
        $dmy_cate = null;
        $stmt = $app['orm.em']->getConnection()->prepare('
                SELECT pc.category_id FROM dtb_product as p, dtb_product_category as pc WHERE p.product_id =' . $id . ' AND p.product_id = pc.product_id AND p.del_flg = 0 AND p.status = 1 ORDER BY pc.rank
                ');
        $stmt->execute();
        $rs = $stmt->fetchAll();

        $pid_ar = array();
        $dmy_count = 0;
        foreach($rs as $cid){
            $dmy = $cid['category_id'];
            $stmt = $app['orm.em']->getConnection()->prepare('
                SELECT pc.product_id FROM dtb_product_category as pc, dtb_product as p WHERE p.product_id = pc.product_id AND pc.category_id =' . $dmy);
            $stmt->execute();
            $rs = $stmt->fetchAll();

            // 表示する個数内にランダムに収まるようシャッフル
            shuffle($rs);

            // もし現在の商品以外のproduct idなら保持
            foreach($rs as $pid){
                if($pid['product_id'] != $id && ($dmy_count < $this->_show_count) ){
                    $pid_ar[] = $pid['product_id'];
                    $dmy_count++;
                }
            }
        }

        // 関連商品自動表示ブロックの設定
        $or_str = '';
        foreach($pid_ar as $pid_item){
            $or_str .= '(ecp.product_id=' . $pid_item . ' AND ecp.product_id = ecpi.product_id AND ecpi.rank=1) or ';
        }
        $or_str = substr($or_str, 0, strlen($or_str)-4);
        if(strlen($or_str) > 1) {

            $stmt_set = $app['orm.em']->getConnection()->prepare('SELECT mode FROM plg_hsd_same_category_setting LIMIT 1');
            $stmt_set->execute();
            $rs_set = $stmt_set->fetchAll();
            if( isset($rs_set[0]['mode']) ){
                $mode = $rs_set[0]['mode'];
            }else{
                $mode = 'price_desc'; //デフォルト
            }

            $israndom = false;
            switch($mode){
                case 'price_desc':
                    // 価格が高い順にソートする場合
                    $sort_str = 'max_price DESC';
                    break;

                case 'price_asc':
                    // 価格が安い順にソートする場合
                    $sort_str = 'max_price ASC';
                    break;

                case 'update_desc':
                    // 更新日が新しい順にソートする場合
                    $sort_str = 'ecp.update_date DESC';
                    break;

                case 'update_asc':
                    // 更新日が古い順にソートする場合
                    $sort_str = 'ecp.update_date ASC';
                    break;

                case 'random':
                    $or_str = "ecp.product_id in (";
                    $dmy_count = 0;
                    foreach($pid_ar as $item){
                        if($dmy_count < $this->_show_count){
                            $or_str .= $item . ',';
                            $dmy_count++;
                        }
                    }
                    $or_str = substr($or_str, 0, strlen($or_str)-1);
                    $or_str .= ')';
                    $israndom = true;
                    break;

            }

            if( $israndom ) {
                $stmt = $app['orm.em']->getConnection()->prepare('SELECT ecp.product_id, ecp.name, ecp.description_detail, ecpi.file_name, (select MIN(in_pcl.price02) FROM dtb_product_class in_pcl WHERE in_pcl.product_id = ecp.product_id GROUP BY in_pcl.product_id) min_price, (select MAX(in_pcl.price02) FROM dtb_product_class in_pcl WHERE in_pcl.product_id = ecp.product_id GROUP BY in_pcl.product_id) max_price FROM dtb_product ecp, dtb_product_image ecpi WHERE ecp.product_id = ecpi.product_id AND ecpi.rank = 1 AND ' . $or_str);
            }else{
                $stmt = $app['orm.em']->getConnection()->prepare('SELECT ecp.product_id, ecp.name, ecp.description_detail, ecpi.file_name, (select MIN(in_pcl.price02) FROM dtb_product_class in_pcl WHERE in_pcl.product_id = ecp.product_id GROUP BY in_pcl.product_id) min_price, (select MAX(in_pcl.price02) FROM dtb_product_class in_pcl WHERE in_pcl.product_id = ecp.product_id GROUP BY in_pcl.product_id) max_price FROM dtb_product ecp, dtb_product_image ecpi WHERE ' . $or_str . ' ORDER BY ' . $sort_str);
            }

            $stmt->execute();
            $this->_rp = $stmt->fetchAll();

            // 表示をシャッフル
            shuffle($this->_rp);
        }

        return $app['view']->render("Block/hsd_same_category.twig", array(
            'title' => $this->_title,
            'max_count' => $this->_show_count,
            'rp_count' => count($this->_rp),
            'hsd_same_category' => $this->_rp,
            'show_type' => $this->_show_type,
            'pagination' => $this->_pagination,
            'navbuttons' => $this->_navbuttons,
            'showloop' => $this->_showloop
        ));

    }

}
