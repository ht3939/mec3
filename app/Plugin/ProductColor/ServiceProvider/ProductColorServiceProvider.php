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

namespace Plugin\ProductColor\ServiceProvider;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class ProductColorServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {

        // 不要？
        $app['eccube.plugin.productcolor.repository.productcolor_plugin'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\ProductColor\Entity\ProductColorPlugin');
        });

        $app['eccube.plugin.productcolor.repository.productcolor'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\ProductColor\Entity\ProductColor');
        });

        $app['eccube.plugin.productcolor.repository.product_productcolor'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\ProductColor\Entity\ProductProductColor');
        });

        // 一覧・登録・修正
        $app->match('/' . $app["config"]["admin_route"] . '/product/productcolor/{id}', '\\Plugin\\ProductColor\\Controller\\ProductColorController::index')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_productcolor');

        // 削除
        $app->match('/' . $app["config"]["admin_route"] . '/product/productcolor/{id}/delete', '\\Plugin\\ProductColor\\Controller\\ProductColorController::delete')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_productcolor_delete');

        // 上
        $app->match('/' . $app["config"]["admin_route"] . '/product/productcolor/{id}/up', '\\Plugin\\ProductColor\\Controller\\ProductColorController::up')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_productcolor_up');

        // 下
        $app->match('/' . $app["config"]["admin_route"] . '/product/productcolor/{id}/down', '\\Plugin\\ProductColor\\Controller\\ProductColorController::down')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_productcolor_down');

        // 型登録
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Plugin\ProductColor\Form\Type\ProductColorType($app);
            return $types;
        }));
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Plugin\ProductColor\Form\Type\Master\ProductColorType($app);
            return $types;
        }));

        // Form Extension
        $app['form.type.extensions'] = $app->share($app->extend('form.type.extensions', function ($extensions) use ($app) {
            $extensions[] = new \Plugin\ProductColor\Form\Extension\Admin\ProductProductColorTypeExtension($app);
            return $extensions;
        }));

        // メッセージ登録
        $app['translator'] = $app->share($app->extend('translator', function ($translator, \Silex\Application $app) {
            $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());

            $file = __DIR__ . '/../Resource/locale/message.' . $app['locale'] . '.yml';
            if (file_exists($file)) {
                $translator->addResource('yaml', $file, $app['locale']);
            }

            return $translator;
        }));
        /**
         * 
         */
        $app['eccube.plugin.productcolor.service'] = $app->share(
            function () use ($app) {
                return new \Plugin\ProductColor\Service\ProductColorService($app);
            }
        );

        // メニュー登録
        $app['config'] = $app->share($app->extend('config', function ($config) {
            $addNavi['id'] = "productcolor";
            $addNavi['name'] = "商品カラー登録";
            $addNavi['url'] = "admin_productcolor";

            $nav = $config['nav'];
            foreach ($nav as $key => $val) {
                if ("product" == $val["id"]) {
                    $nav[$key]['child'][] = $addNavi;
                }
            }

            $config['nav'] = $nav;
            return $config;
        }));
    }

    public function boot(BaseApplication $app)
    {
    }
}
