<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
* http://www.lockon.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\CloseProductRedirect;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CloseProductRedirect
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function onRenderAdminProductPage(FilterResponseEvent $event)
    {
        /* @var $HookPoint \Plugin\CloseProductRedirect\HookPoint\Admin\AdminProductPageService */
        $HookPoint = $this->app['eccube.plugin.service.cpr.admin_product_page'];
        $HookPoint->onRenderBefore($event);
    }

    public function onControllerBeforeProductDetail()
    {
        /* @var $HookPoint \Plugin\CloseProductRedirect\HookPoint\Front\ProductDetailService */
        $HookPoint = $this->app['eccube.plugin.service.cpr.product_detail'];
        $HookPoint->onControllerBefore();
    }

}
