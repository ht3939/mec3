<?php
/*
 * Copyright(c) 2016 SYSTEM_KD
 */

namespace Plugin\AdminProductListPlus;

use Eccube\Event\TemplateEvent;
use Eccube\Common\Constant;

class AdminProductListPlus
{
    private $app;

    private $arrVerCheck;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 商品一覧
     *
     * @param TemplateEvent $event
     */
    public function onAdminProductIndexTwig(TemplateEvent $event) {

        /* @var $TwigRenderService \Plugin\AdminProductListPlus\Service\TwigRenderService */
        $TwigRenderService = $this->app['adminproductlistplus.service.twigrenderservice'];
        $TwigRenderService->initTwigRenderControl($event);

        // 規格後に追加
        $search = '<div id="result_list__name--{{ Product.id }}" class="item_detail td">';

        // 価格,在庫追加
        $insert = file_get_contents('../app/Plugin/AdminProductListPlus/Resource/template/admin/Product/price_stock.twig');
        $TwigRenderService->twigInsert($search, $insert, 9);

        if(Constant::VERSION != "3.0.9") {
            // 3.0.10 以降を対象
            // タグ情報追加
            $insert = file_get_contents('../app/Plugin/AdminProductListPlus/Resource/template/admin/Product/add_tag.twig');
            $TwigRenderService->twigInsert($search, $insert, 9);
        }

        $event->setSource($TwigRenderService->getContent());
    }
}
