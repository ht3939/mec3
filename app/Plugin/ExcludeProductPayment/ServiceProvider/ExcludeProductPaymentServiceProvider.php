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

namespace Plugin\ExcludeProductPayment\ServiceProvider;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class ExcludeProductPaymentServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        // admin
        $app->match('/' . $app["config"]["admin_route"] . '/plugin/close_product_redirect/config', '\Plugin\ExcludeProductPayment\Controller\Admin\Plugin\ConfigController::edit')->bind('plugin_ExcludeProductPayment_config');

        // front
        $app->match('/redirect_page_guide/{redirectAction}', '\Plugin\ExcludeProductPayment\Controller\Front\PageGuideController::index')->bind('cpr_redirect_guide')->assert('redirectAction', '\d+');

        // Service
        $app['eccube.plugin.service.cpr.config'] = $app->share(function () use ($app) {
            return new \Plugin\ExcludeProductPayment\Service\ConfigService($app);
        });
        $app['eccube.plugin.service.cpr.util'] = $app->share(function () use ($app) {
            return new \Plugin\ExcludeProductPayment\Service\UtilService($app);
        });

        // HookPointService
        // admin
        $app['eccube.plugin.service.cpr.hook_base'] = $app->share(function () use ($app) {
            return new \Plugin\ExcludeProductPayment\HookPoint\HookBaseService($app);
        });
        $app['eccube.plugin.service.cpr.admin_product_page'] = $app->share(function () use ($app) {
            return new \Plugin\ExcludeProductPayment\HookPoint\Admin\AdminProductPageService($app);
        });
        // front
        $app['eccube.plugin.service.cpr.product_detail'] = $app->share(function () use ($app) {
            return new \Plugin\ExcludeProductPayment\HookPoint\Front\ProductDetailService($app);
        });

        // Repositoy
        $app['eccube.plugin.repository.cpr.plugin'] = function () use ($app) {
            return $app['orm.em']->getRepository('\Plugin\ExcludeProductPayment\Entity\CprPlugin');
        };
        $app['eccube.plugin.repository.cpr.product_redirect'] = function () use ($app) {
            return $app['orm.em']->getRepository('\Plugin\ExcludeProductPayment\Entity\CprProductRedirect');
        };

        // Formの定義
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Plugin\ExcludeProductPayment\Form\Type\Admin\ConfigType($app);
            $types[] = new \Plugin\ExcludeProductPayment\Form\Type\Admin\ProductRedirectType($app);

            return $types;
        }));

        // Form拡張の定義
        $app['form.type.extensions'] = $app->share($app->extend('form.type.extensions', function ($extensions) use ($app) {
            $extensions[] = new \Plugin\ExcludeProductPayment\Form\Extension\ProductTypeExtension($app);

            return $extensions;
        }));

    }

    public function boot(BaseApplication $app)
    {
    }
}