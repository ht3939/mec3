<?php

namespace Plugin\MakerRelatedProduct\Controller\Block;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Plugin\MakerRelatedProduct\Entity\MakerRelatedProduct;


class MakerRelatedProductController
{
    //タイトル
    private $_title = 'この商品と同じメーカーの商品';

    // 表示個数
    private $_show_count = 4; //初期値4

    // 関連商品用データ
    private $_rp = null;

    // 価格の表示/非表示
    private $_show_price = null;

    /**
     * MakerRelatedProduct画面
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        $id = $app['request']->attributes->get('id');

        /*
         * 
         */
        if( $id ){
            $sql = 'SELECT ecp.product_id, ecp.name, ecp.description_detail, ecpi.file_name, (select MIN(in_pcl.price02) FROM dtb_product_class in_pcl WHERE in_pcl.product_id = ecp.product_id AND in_pcl.del_flg <> 1 GROUP BY in_pcl.product_id) min_price, (select MAX(in_pcl.price02) FROM dtb_product_class in_pcl WHERE in_pcl.product_id = ecp.product_id AND in_pcl.del_flg <> 1 GROUP BY in_pcl.product_id) max_price from dtb_product ecp inner join dtb_product_image ecpi on ecp.product_id = ecpi.product_id AND ecpi.rank=1 inner join plg_product_maker opm on ecp.product_id = opm.product_id where opm.maker_id in (select pm.maker_id from dtb_product p inner join plg_product_maker pm on p.product_id = pm.product_id where p.product_id ='.$id.')';

            $stmt = $app['orm.em']->getConnection()->prepare($sql);
            $stmt->execute();
            $this->_rp = $stmt->fetchAll();

        }

        /*
         * 現在の商品id をセッションに保持
         */
        $_SESSION['ec_save_pr_id'] = $id;

        return $app['view']->render("Block/maker_related_product.twig", array(
            'title' => $this->_title,
            'max_count' => $this->_show_count,
            'rp_count' => count($this->_rp),
            'maker_related_product' => $this->_rp,
            'show_price' => $this->_show_price
        ));

    }

}
