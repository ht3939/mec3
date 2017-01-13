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

namespace Plugin\CloseProductRedirect\ServiceProvider;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class CloseProductRedirectServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        // admin
        $app->match('/' . $app["config"]["admin_route"] . '/plugin/close_product_redirect/config', '\Plugin\CloseProductRedirect\Controller\Admin\Plugin\ConfigController::edit')->bind('plugin_CloseProductRedirect_config');

        // front
        $app->match('/redirect_page_guide/{redirectAction}', '\Plugin\CloseProductRedirect\Controller\Front\PageGuideController::index')->bind('cpr_redirect_guide')->assert('redirectAction', '\d+');

        // Service
        $app['eccube.plugin.service.cpr.config'] = $app->share(function () use ($app) {
            return new \Plugin\CloseProductRedirect\Service\ConfigService($app);
        });
        $app['eccube.plugin.service.cpr.util'] = $app->share(function () use ($app) {
            return new \Plugin\CloseProductRedirect\Service\UtilService($app);
        });

        // HookPointService
        // admin
        $app['eccube.plugin.service.cpr.hook_base'] = $app->share(function () use ($app) {
            return new \Plugin\CloseProductRedirect\HookPoint\HookBaseService($app);
        });
        $app['eccube.plugin.service.cpr.admin_product_page'] = $app->share(function () use ($app) {
            return new \Plugin\CloseProductRedirect\HookPoint\Admin\AdminProductPageService($app);
        });
        // front
        $app['eccube.plugin.service.cpr.product_detail'] = $app->share(function () use ($app) {
            return new \Plugin\CloseProductRedirect\HookPoint\Front\ProductDetailService($app);
        });

        // Repositoy
        $app['eccube.plugin.repository.cpr.plugin'] = function () use ($app) {
            return $app['orm.em']->getRepository('\Plugin\CloseProductRedirect\Entity\CprPlugin');
        };
        $app['eccube.plugin.repository.cpr.product_redirect'] = function () use ($app) {
            return $app['orm.em']->getRepository('\Plugin\CloseProductRedirect\Entity\CprProductRedirect');
        };

        // Formの定義
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Plugin\CloseProductRedirect\Form\Type\Admin\ConfigType($app);
            $types[] = new \Plugin\CloseProductRedirect\Form\Type\Admin\ProductRedirectType($app);

            return $types;
        }));

        // Form拡張の定義
        $app['form.type.extensions'] = $app->share($app->extend('form.type.extensions', function ($extensions) use ($app) {
            $extensions[] = new \Plugin\CloseProductRedirect\Form\Extension\ProductTypeExtension($app);

            return $extensions;
        }));

    }

    public function boot(BaseApplication $app)
    {
    }
}