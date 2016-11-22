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

namespace Plugin\RecommendSimRanking\ServiceProvider;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;

class RecommendSimRankingServiceProvider implements ServiceProviderInterface
{

    public function register(BaseApplication $app)
    {
        // おすすめ情報テーブルリポジトリ
        $app['eccube.plugin.recommendsimranking.repository.recommendsimranking_product'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\RecommendSimRanking\Entity\RecommendSimRankingProduct');
        });

        // おすすめ商品の一覧
        $app->match('/' . $app["config"]["admin_route"] . '/recommendsimranking', '\Plugin\RecommendSimRanking\Controller\RecommendSimRankingController::index')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_recommendsimranking_list');

        // おすすめ商品の新規先
        $app->match('/' . $app["config"]["admin_route"] . '/recommendsimranking/new', '\Plugin\RecommendSimRanking\Controller\RecommendSimRankingController::create')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_recommendsimranking_new');

        // おすすめ商品の新規作成・編集確定
        $app->match('/' . $app["config"]["admin_route"] . '/recommendsimranking/commit', '\Plugin\RecommendSimRanking\Controller\RecommendSimRankingController::commit')
        ->value('id', null)->assert('id', '\d+|')
        ->bind('admin_recommendsimranking_commit');

        // おすすめ商品の編集
        $app->match('/' . $app["config"]["admin_route"] . '/recommendsimranking/edit/{id}', '\Plugin\RecommendSimRanking\Controller\RecommendSimRankingController::edit')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_recommendsimranking_edit');

        // おすすめ商品の削除
        $app->match('/' . $app["config"]["admin_route"] . '/recommendsimranking/delete/{id}', '\Plugin\RecommendSimRanking\Controller\RecommendSimRankingController::delete')
        ->value('id', null)->assert('id', '\d+|')
        ->bind('admin_recommendsimranking_delete');

        // おすすめ商品のランク移動（上）
        $app->match('/' . $app["config"]["admin_route"] . '/recommendsimranking/rank_up/{id}', '\Plugin\RecommendSimRanking\Controller\RecommendSimRankingController::rankUp')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_recommendsimranking_rank_up');

        // おすすめ商品のランク移動（下）
        $app->match('/' . $app["config"]["admin_route"] . '/recommendsimranking/rank_down/{id}', '\Plugin\RecommendSimRanking\Controller\RecommendSimRankingController::rankDown')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_recommendsimranking_rank_down');

        // 商品検索画面表示
        $app->post('/' . $app["config"]["admin_route"] . '/recommendsimranking/search/product', '\Plugin\RecommendSimRanking\Controller\RecommendSimRankingSearchModelController::searchProduct')
            ->bind('admin_recommendsimranking_search_product');

        // ブロック
        $app->match('/block/recommendsimranking_product_block', '\Plugin\RecommendSimRanking\Controller\Block\RecommendSimRankingController::index')
            ->bind('block_recommendsimranking_product_block');


        // 型登録
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Plugin\RecommendSimRanking\Form\Type\RecommendSimRankingProductType($app);
            return $types;
        }));

        // サービスの登録
        $app['eccube.plugin.recommendsimranking.service.recommendsimranking'] = $app->share(function () use ($app) {
            return new \Plugin\RecommendSimRanking\Service\RecommendSimRankingService($app);
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
            $addNavi['id'] = 'admin_recommendsimranking';
            $addNavi['name'] = 'SIM商品ランキング管理';
            $addNavi['url'] = 'admin_recommendsimranking_list';
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
