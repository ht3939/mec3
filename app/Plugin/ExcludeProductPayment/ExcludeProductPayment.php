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

namespace Plugin\ExcludeProductPayment;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ExcludeProductPayment
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function onRenderAdminProductPage(FilterResponseEvent $event)
    {
        /* @var $HookPoint \Plugin\ExcludeProductPayment\HookPoint\Admin\AdminProductPageService */
        $HookPoint = $this->app['eccube.plugin.service.epp.admin_product_page'];
        $HookPoint->onRenderBefore($event);
    }

    public function onControllerBeforeProductDetail()
    {
        /* @var $HookPoint \Plugin\ExcludeProductPayment\HookPoint\Front\ProductDetailService */
        $HookPoint = $this->app['eccube.plugin.service.epp.product_detail'];
        $HookPoint->onControllerBefore();
    }

}
