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

namespace Plugin\recommendsumahorankingSumahoRanking\ServiceProvider;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;

class recommendsumahorankingSumahoRankingServiceProvider implements ServiceProviderInterface
{

    public function register(BaseApplication $app)
    {
        // おすすめ情報テーブルリポジトリ
        $app['eccube.plugin.recommendsumahoranking.repository.recommendsumahoranking_product'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\recommendsumahorankingSumahoRanking\Entity\recommendsumahorankingSumahoRankingProduct');
        });

        // おすすめ商品の一覧
        $app->match('/' . $app["config"]["admin_route"] . '/recommendsumahoranking', '\Plugin\recommendsumahorankingSumahoRanking\Controller\recommendsumahorankingSumahoRankingController::index')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_recommendsumahoranking_list');

        // おすすめ商品の新規先
        $app->match('/' . $app["config"]["admin_route"] . '/recommendsumahoranking/new', '\Plugin\recommendsumahorankingSumahoRanking\Controller\recommendsumahorankingSumahoRankingController::create')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_recommendsumahoranking_new');

        // おすすめ商品の新規作成・編集確定
        $app->match('/' . $app["config"]["admin_route"] . '/recommendsumahoranking/commit', '\Plugin\recommendsumahorankingSumahoRanking\Controller\recommendsumahorankingSumahoRankingController::commit')
        ->value('id', null)->assert('id', '\d+|')
        ->bind('admin_recommendsumahoranking_commit');

        // おすすめ商品の編集
        $app->match('/' . $app["config"]["admin_route"] . '/recommendsumahoranking/edit/{id}', '\Plugin\recommendsumahorankingSumahoRanking\Controller\recommendsumahorankingSumahoRankingController::edit')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_recommendsumahoranking_edit');

        // おすすめ商品の削除
        $app->match('/' . $app["config"]["admin_route"] . '/recommendsumahoranking/delete/{id}', '\Plugin\recommendsumahorankingSumahoRanking\Controller\recommendsumahorankingSumahoRankingController::delete')
        ->value('id', null)->assert('id', '\d+|')
        ->bind('admin_recommendsumahoranking_delete');

        // おすすめ商品のランク移動（上）
        $app->match('/' . $app["config"]["admin_route"] . '/recommendsumahoranking/rank_up/{id}', '\Plugin\recommendsumahorankingSumahoRanking\Controller\recommendsumahorankingSumahoRankingController::rankUp')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_recommendsumahoranking_rank_up');

        // おすすめ商品のランク移動（下）
        $app->match('/' . $app["config"]["admin_route"] . '/recommendsumahoranking/rank_down/{id}', '\Plugin\recommendsumahorankingSumahoRanking\Controller\recommendsumahorankingSumahoRankingController::rankDown')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_recommendsumahoranking_rank_down');

        // 商品検索画面表示
        $app->post('/' . $app["config"]["admin_route"] . '/recommendsumahoranking/search/product', '\Plugin\recommendsumahorankingSumahoRanking\Controller\recommendsumahorankingSumahoRankingSearchModelController::searchProduct')
            ->bind('admin_recommendsumahoranking_search_product');

        // ブロック
        $app->match('/block/recommendsumahoranking_product_block', '\Plugin\recommendsumahorankingSumahoRanking\Controller\Block\recommendsumahorankingSumahoRankingController::index')
            ->bind('block_recommendsumahoranking_product_block');


        // 型登録
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Plugin\recommendsumahorankingSumahoRanking\Form\Type\recommendsumahorankingSumahoRankingProductType($app);
            return $types;
        }));

        // サービスの登録
        $app['eccube.plugin.recommendsumahoranking.service.recommendsumahoranking'] = $app->share(function () use ($app) {
            return new \Plugin\recommendsumahorankingSumahoRanking\Service\recommendsumahorankingSumahoRankingService($app);
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
            $addNavi['id'] = 'admin_recommendsumahoranking';
            $addNavi['name'] = 'おすすめ管理';
            $addNavi['url'] = 'admin_recommendsumahoranking_list';
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
