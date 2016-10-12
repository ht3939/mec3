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

namespace Plugin\ProductClassEx\ServiceProvider;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;

class ProductClassExServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {

        // 不要？
        $app['eccube.plugin.product_classex.repository.product_classex_plugin'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\ProductClassEx\Entity\ProductClassExPlugin');
        });

        // 商品規格拡張用リポジトリ
        $app['eccube.plugin.product_classex.repository.product_classex'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\ProductClassEx\Entity\ProductClassEx');
        });


        // 商品リポジトリ
        $app['eccube.plugin.product_classex.repository.productex'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\ProductClassEx\Entity\ProductEx');
        });

        // 商品規格画像リポジトリ
        $app['eccube.plugin.product_classex.repository.product_classex_image'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\ProductClassEx\Entity\ProductClassExImage');
        });


        // ===========================================
        // 配信内容設定
        // ===========================================
        // 配信設定検索・一覧
        $app->match('/' . $app["config"]["admin_route"] . '/productclassex/{id}', '\\Plugin\\ProductClassEx\\Controller\\ProductClassExController::index')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_productclassex');
        $app->post('/' . $app["config"]["admin_route"] . '/productclassex/{id}/edit', '\\Plugin\\ProductClassEx\\Controller\\ProductClassExController::edit')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_productclassex_edit');
        $app->post('/' . $app["config"]["admin_route"] . '/productclassex/image/add', '\\Plugin\\ProductClassEx\\Controller\\ProductClassExController::addImage')
            ->bind('admin_productclassex_image_add');

            //'/product/product/class/edit/{id}', '\Eccube\Controller\Admin\Product\ProductClassController::edit')->assert('id', '\d+')->bind('admin_product_product_class_edit');

        // 型登録
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
                // テンプレート設定
                $types[] = new \Plugin\ProductClassEx\Form\Type\ProductClassExType($app);

            return $types;
        }));

        // Form Extension
        /*
        $app['form.type.extensions'] = $app->share($app->extend('form.type.extensions', function ($extensions) use ($app) {
            $extensions[] = new \Plugin\MailMagazine\Form\Extension\EntryMailMagazineTypeExtension($app);
            $extensions[] = new \Plugin\MailMagazine\Form\Extension\CustomerMailMagazineTypeExtension($app);
            return $extensions;
        }));
        */

        // -----------------------------
        // サービス
        // -----------------------------
        /*
        $app['eccube.plugin.mail_magazine.service.mail'] = $app->share(function () use ($app) {
                return new \Plugin\MailMagazine\Service\MailMagazineService($app);
            });
        */

        // -----------------------------
        // メッセージ登録
        // -----------------------------
        /*
        $app['translator'] = $app->share($app->extend('translator', function ($translator, \Silex\Application $app) {
            $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());

            $file = __DIR__ . '/../Resource/locale/message.' . $app['locale'] . '.yml';
            if (file_exists($file)) {
                $translator->addResource('yaml', $file, $app['locale']);
            }

            return $translator;
        }));
        */

        // メニュー登録
        /*
        $app['config'] = $app->share($app->extend('config', function ($config) {
            $addNavi = array(
                'id' => 'mailmagazine',
                'name' => "メルマガ管理",
                'has_child' => true,
                'icon' => 'cb-comment',
                'child' => array(
                    array(
                        'id' => "mailmagazine",
                        'name' => "配信内容設定",
                        'url' => "admin_mail_magazine",
                    ),
                    array(
                        'id' => "mailmagazine_template",
                        'name' => "テンプレート設定",
                        'url' => "admin_mail_magazine_template",
                    ),
                    array(
                        'id' => "mailmagazine_history",
                        'name' => "配信履歴",
                        'url' => "admin_mail_magazine_history",
                    ),
                ),
            );

            $nav = $config['nav'];
            foreach ($nav as $key => $val) {
                if ("setting" == $val['id']) {
                    array_splice($nav, $key, 0, array($addNavi));
                    break;
                }
            }
            $config['nav'] = $nav;
            return $config;
        }));
        */
    }

    public function boot(BaseApplication $app)
    {
    }
}
