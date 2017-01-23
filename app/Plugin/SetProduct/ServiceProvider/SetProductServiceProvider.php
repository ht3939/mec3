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

namespace Plugin\SetProduct\ServiceProvider;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class SetProductServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {

        // 不要？
        $app['eccube.plugin.setproduct.repository.setproduct_plugin'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\SetProduct\Entity\SetProductPlugin');
        });

        // // メーカーテーブル用リポジトリ
        // $app['eccube.plugin.setproduct.repository.setproduct'] = $app->share(function () use ($app) {
        //     return $app['orm.em']->getRepository('Plugin\SetProduct\Entity\SetProduct');
        // });

        $app['eccube.plugin.setproduct.repository.product_setproduct'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\SetProduct\Entity\ProductSetProduct');
        });

        // 一覧・登録・修正
        // $app->match('/' . $app["config"]["admin_route"] . '/product/setproduct/{id}', '\\Plugin\\SetProduct\\Controller\\SetProductController::index')
        //     ->value('id', null)->assert('id', '\d+|')
        //     ->bind('admin_setproduct');

        // // 削除
        // $app->match('/' . $app["config"]["admin_route"] . '/product/setproduct/{id}/delete', '\\Plugin\\SetProduct\\Controller\\SetProductController::delete')
        //     ->value('id', null)->assert('id', '\d+|')
        //     ->bind('admin_setproduct_delete');

        // // 上
        // $app->match('/' . $app["config"]["admin_route"] . '/product/setproduct/{id}/up', '\\Plugin\\SetProduct\\Controller\\SetProductController::up')
        //     ->value('id', null)->assert('id', '\d+|')
        //     ->bind('admin_setproduct_up');

        // // 下
        // $app->match('/' . $app["config"]["admin_route"] . '/product/setproduct/{id}/down', '\\Plugin\\SetProduct\\Controller\\SetProductController::down')
        //     ->value('id', null)->assert('id', '\d+|')
        //     ->bind('admin_setproduct_down');

        // 型登録
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Plugin\SetProduct\Form\Type\SetProductType($app);
            return $types;
        }));

        // Form Extension
        $app['form.type.extensions'] = $app->share($app->extend('form.type.extensions', function ($extensions) use ($app) {
            $extensions[] = new \Plugin\SetProduct\Form\Extension\Admin\ProductSetProductTypeExtension($app);
            return $extensions;
        }));

        // サービスの登録
        $app['eccube.plugin.setproduct.service.setproduct'] = $app->share(function () use ($app) {
            return new \Plugin\SetProduct\Service\SetProductService($app);
        });

        // メッセージ登録
        $app['translator'] = $app->share($app->extend('translator', function ($translator, \Silex\Application $app) {
            $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());

            $file = __DIR__ . '/../Resource/locale/message.' . $app['locale'] . '.yml';
            if (file_exists($file)) {
                $translator->addResource('yaml', $file, $app['locale']);
            }

            return $translator;
        }));

        // // メニュー登録
        // $app['config'] = $app->share($app->extend('config', function ($config) {
        //     $addNavi['id'] = "setproduct";
        //     $addNavi['name'] = "セット商品設定";
        //     $addNavi['url'] = "admin_setproduct";

        //     $nav = $config['nav'];
        //     foreach ($nav as $key => $val) {
        //         if ("product" == $val["id"]) {
        //             $nav[$key]['child'][] = $addNavi;
        //         }
        //     }

        //     $config['nav'] = $nav;
        //     return $config;
        // }));
    }

    public function boot(BaseApplication $app)
    {
    }
}
