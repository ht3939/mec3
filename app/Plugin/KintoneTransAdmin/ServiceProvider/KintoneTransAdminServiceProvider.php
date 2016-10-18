<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Plugin\KintoneTransAdmin\ServiceProvider;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;

class KintoneTransAdminServiceProvider implements ServiceProviderInterface
{

    public function register(BaseApplication $app)
    {
        // おすすめ情報テーブルリポジトリ
        $app['eccube.plugin.kintonetransadmin.repository.kintonetransadmin_product'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\KintoneTransAdmin\Entity\KintoneTransAdminProduct');
        });

        // おすすめ商品の一覧
        $app->match('/' . $app["config"]["admin_route"] . '/kintonetransadmin', '\Plugin\KintoneTransAdmin\Controller\KintoneTransAdminController::index')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_kintonetransadmin_list');

        // おすすめ商品の新規先
        $app->match('/' . $app["config"]["admin_route"] . '/kintonetransadmin/new', '\Plugin\KintoneTransAdmin\Controller\KintoneTransAdminController::create')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_kintonetransadmin_new');

        // おすすめ商品の新規作成・編集確定
        $app->match('/' . $app["config"]["admin_route"] . '/kintonetransadmin/commit', '\Plugin\KintoneTransAdmin\Controller\KintoneTransAdminController::commit')
        ->value('id', null)->assert('id', '\d+|')
        ->bind('admin_kintonetransadmin_commit');

        // おすすめ商品の編集
        $app->match('/' . $app["config"]["admin_route"] . '/kintonetransadmin/edit/{id}', '\Plugin\KintoneTransAdmin\Controller\KintoneTransAdminController::edit')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_kintonetransadmin_edit');

        // おすすめ商品の削除
        $app->match('/' . $app["config"]["admin_route"] . '/kintonetransadmin/delete/{id}', '\Plugin\KintoneTransAdmin\Controller\KintoneTransAdminController::delete')
        ->value('id', null)->assert('id', '\d+|')
        ->bind('admin_kintonetransadmin_delete');

        // 商品検索画面表示
        $app->post('/' . $app["config"]["admin_route"] . '/kintonetransadmin/search/product', '\Plugin\KintoneTransAdmin\Controller\KintoneTransAdminSearchModelController::searchProduct')
            ->bind('admin_kintonetransadmin_search_product');

        // ブロック
        $app->match('/block/kintonetransadmin_product_block', '\Plugin\KintoneTransAdmin\Controller\Block\KintoneTransAdminController::index')
            ->bind('block_kintonetransadmin_product_block');


        // 型登録
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Plugin\KintoneTransAdmin\Form\Type\KintoneTransAdminProductType($app);
            return $types;
        }));

        // サービスの登録
        $app['eccube.plugin.kintonetransadmin.service.kintonetransadmin'] = $app->share(function () use ($app) {
            return new \Plugin\KintoneTransAdmin\Service\KintoneTransAdminService($app);
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

        // メニュー登録
        $app['config'] = $app->share($app->extend('config', function ($config) {
            $addNavi['id'] = 'admin_kintonetransadmin';
            $addNavi['name'] = '連携管理';
            $addNavi['url'] = 'admin_kintonetransadmin_list';
            $nav = $config['nav'];
            foreach ($nav as $key => $val) {
                if ('content' == $val['id']) {
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
