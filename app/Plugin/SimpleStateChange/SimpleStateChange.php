<?php
/*
 * Copyright(c) 2016 SYSTEM_KD
 */


namespace Plugin\SimpleStateChange;

use Eccube\Event\TemplateEvent;

class SimpleStateChange
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function onAdminProductIndexTwig(TemplateEvent $event)
    {

        /* @var $TwigRenderService \Plugin\SimpleStateChange\Service\TwigRenderService */
        $TwigRenderService = $this->app['ssc.service.twigrenderservice'];
        $TwigRenderService->initTwigRenderControl($event);

        // 切り替えボタン追加
        $search = '<span  id="result_list__code--{{ Product.id }}">';
        $view = 'SimpleStateChange/Resource/template/admin/Product/add_index.twig';
        $insert = $TwigRenderService->readTwigFile($view);
        $TwigRenderService->twigInsert($search, $insert, 5);

        // js 追加
        $search = '{% endblock javascript %}';
        $view = 'SimpleStateChange/Resource/template/admin/Product/add_index_js.twig';
        $replace = $TwigRenderService->readTwigFile($view);
        $replace .= $replace.$search;
        $TwigRenderService->twigReplace($search, $replace);

        // 一括処理機能
        $search = '<li id="result_list__pagemax_menu" class="dropdown">';
        $insert  = '<li id="result_list__status_menu" class="dropdown">';
        $insert .= '<a class="dropdown-toggle" data-toggle="dropdown">種別一括変更<svg class="cb cb-angle-down icon_down"><use xlink:href="#cb-angle-down"></svg></a>';
        $insert .= '<ul class="dropdown-menu">';
        $insert .= '<li><a href="#" data-toggle="modal" onclick="statusModalShow(1); return false;">公開状態一括変更</a></li>';
        $insert .= '</ul>';
        $TwigRenderService->twigInsert($search, $insert, 10);

        /* js, モーダル用div 追加 */
        $search = '{% endblock javascript %}';
        $view = 'SimpleStateChange/Resource/template/admin/Product/status_update_js.twig';
        $replace = $TwigRenderService->readTwigFile($view);
        $TwigRenderService->twigReplace($search, $replace);

        // モーダル存在チェック
        $searchEndBlock = "{% endblock modal %}";
        $endBlock = $TwigRenderService->getRowContents($searchEndBlock);

        if($endBlock == "") {
            // modal なし
            $search = '{% endblock %}';
            $view = 'SimpleStateChange/Resource/template/admin/Product/status_update.twig';
            $insert = "{% block modal %}" . $TwigRenderService->readTwigFile($view);
            $TwigRenderService->twigInsert(array($search, 2), $insert);

        } else {

            // modal あり
            $search = $searchEndBlock;
            $view = 'SimpleStateChange/Resource/template/admin/Product/status_update.twig';
            $insert = $TwigRenderService->readTwigFile($view);
            $TwigRenderService->twigReplace($searchEndBlock, $insert);
        }



        $event->setSource($TwigRenderService->getContent());
    }
}
