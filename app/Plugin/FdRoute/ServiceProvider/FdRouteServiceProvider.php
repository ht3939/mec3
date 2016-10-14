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

namespace Plugin\FdRoute\ServiceProvider;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;

class FdRouteServiceProvider implements ServiceProviderInterface
{

    public function register(BaseApplication $app)
    {
        // おすすめ情報テーブルリポジトリ
        $app['eccube.plugin.fdroute.repository.fdroute_product'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\FdRoute\Entity\FdRouteProduct');
        });

        // おすすめ商品の一覧
        $app->match('/' . $app["config"]["admin_route"] . '/fdroute', '\Plugin\FdRoute\Controller\FdRouteController::index')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_fdroute_list');

        // おすすめ商品の新規先
        $app->match('/' . $app["config"]["admin_route"] . '/fdroute/new', '\Plugin\FdRoute\Controller\FdRouteController::create')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_fdroute_new');

        // おすすめ商品の新規作成・編集確定
        $app->match('/' . $app["config"]["admin_route"] . '/fdroute/commit', '\Plugin\FdRoute\Controller\FdRouteController::commit')
        ->value('id', null)->assert('id', '\d+|')
        ->bind('admin_fdroute_commit');

        // おすすめ商品の編集
        $app->match('/' . $app["config"]["admin_route"] . '/fdroute/edit/{id}', '\Plugin\FdRoute\Controller\FdRouteController::edit')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_fdroute_edit');

        // おすすめ商品の削除
        $app->match('/' . $app["config"]["admin_route"] . '/fdroute/delete/{id}', '\Plugin\FdRoute\Controller\FdRouteController::delete')
        ->value('id', null)->assert('id', '\d+|')
        ->bind('admin_fdroute_delete');

        // おすすめ商品のランク移動（上）
        $app->match('/' . $app["config"]["admin_route"] . '/fdroute/rank_up/{id}', '\Plugin\FdRoute\Controller\FdRouteController::rankUp')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_fdroute_rank_up');

        // おすすめ商品のランク移動（下）
        $app->match('/' . $app["config"]["admin_route"] . '/fdroute/rank_down/{id}', '\Plugin\FdRoute\Controller\FdRouteController::rankDown')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_fdroute_rank_down');

        // 商品検索画面表示
        $app->post('/' . $app["config"]["admin_route"] . '/fdroute/search/product', '\Plugin\FdRoute\Controller\FdRouteSearchModelController::searchProduct')
            ->bind('admin_fdroute_search_product');

        // ブロック
        $app->match('/block/fdroute_product_block', '\Plugin\FdRoute\Controller\Block\FdRouteController::index')
            ->bind('block_fdroute_product_block');


        // 型登録
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Plugin\FdRoute\Form\Type\FdRouteProductType($app);
            return $types;
        }));

        // サービスの登録
        $app['eccube.plugin.fdroute.service.fdroute'] = $app->share(function () use ($app) {
            return new \Plugin\FdRoute\Service\FdRouteService($app);
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
            $addNavi['id'] = 'admin_fdroute';
            $addNavi['name'] = 'FDルート管理';
            $addNavi['url'] = 'admin_fdroute_list';
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
