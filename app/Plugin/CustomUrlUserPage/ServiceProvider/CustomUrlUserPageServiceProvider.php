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

namespace Plugin\CustomUrlUserPage\ServiceProvider;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;

class CustomUrlUserPageServiceProvider implements ServiceProviderInterface
{

    public function register(BaseApplication $app)
    {
        // おすすめ情報テーブルリポジトリ
        $app['eccube.plugin.customurluserpage.repository.customurluserpage'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\CustomUrlUserPage\Entity\CustomUrlUserPage');
        });

        // おすすめ商品の一覧
        $app->match('/' . $app["config"]["admin_route"] . '/customurluserpage', '\Plugin\CustomUrlUserPage\Controller\Admin\CustomUrlUserPageController::index')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_customurluserpage_list');

        // おすすめ商品の新規先
        $app->match('/' . $app["config"]["admin_route"] . '/customurluserpage/new', '\Plugin\CustomUrlUserPage\Controller\Admin\CustomUrlUserPageController::create')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_customurluserpage_new');

        // おすすめ商品の新規作成・編集確定
        $app->match('/' . $app["config"]["admin_route"] . '/customurluserpage/commit', '\Plugin\CustomUrlUserPage\Controller\Admin\CustomUrlUserPageController::commit')
        ->value('id', null)->assert('id', '\d+|')
        ->bind('admin_customurluserpage_commit');

        // おすすめ商品の編集
        $app->match('/' . $app["config"]["admin_route"] . '/customurluserpage/edit/{id}', '\Plugin\CustomUrlUserPage\Controller\Admin\CustomUrlUserPageController::edit')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_customurluserpage_edit');

        // おすすめ商品の削除
        $app->match('/' . $app["config"]["admin_route"] . '/customurluserpage/delete/{id}', '\Plugin\CustomUrlUserPage\Controller\Admin\CustomUrlUserPageController::delete')
        ->value('id', null)->assert('id', '\d+|')
        ->bind('admin_customurluserpage_delete');

        // おすすめ商品のランク移動（上）
        $app->match('/' . $app["config"]["admin_route"] . '/customurluserpage/rank_up/{id}', '\Plugin\CustomUrlUserPage\Controller\Admin\CustomUrlUserPageController::rankUp')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_customurluserpage_rank_up');

        // おすすめ商品のランク移動（下）
        $app->match('/' . $app["config"]["admin_route"] . '/customurluserpage/rank_down/{id}', '\Plugin\CustomUrlUserPage\Controller\Admin\CustomUrlUserPageController::rankDown')
            ->value('id', null)->assert('id', '\d+|')
            ->bind('admin_customurluserpage_rank_down');

        // 商品検索画面表示
        $app->post('/' . $app["config"]["admin_route"] . '/customurluserpage/search/userpage', '\Plugin\CustomUrlUserPage\Controller\Admin\CustomUrlUserPageSearchModelController::searchUserPage')
            ->bind('admin_customurluserpage_search_userpage');

        // ブロック
        $app->match('/block/customurluserpage_block/{listtype}', '\Plugin\CustomUrlUserPage\Controller\Block\CustomUrlUserPageController::index')
            ->bind('block_customurluserpage_block');

        // 型登録
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Plugin\CustomUrlUserPage\Form\Type\CustomUrlUserPageProductType($app);
            $types[] = new \Plugin\CustomUrlUserPage\Form\Type\Admin\SearchPageLayoutType($app);
            return $types;
        }));

        // サービスの登録
        $app['eccube.plugin.customurluserpage.service.customurluserpage'] = $app->share(function () use ($app) {
            return new \Plugin\CustomUrlUserPage\Service\CustomUrlUserPageService($app);
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

        //カスタムURLの定義反映
        $this->bind_customurl($app);

        // メニュー登録
        $app['config'] = $app->share($app->extend('config', function ($config) {
            $addNavi['id'] = 'admin_customurluserpage';
            $addNavi['name'] = 'カスタムURL管理';
            $addNavi['url'] = 'admin_customurluserpage_list';
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

    private function bind_customurl(BaseApplication $app){

        $CustomUrlUserPageRepo = $app['eccube.plugin.customurluserpage.repository.customurluserpage'];

        $Indexes = $CustomUrlUserPageRepo->findBy(array('index_flg'=>1,'del_flg'=>0));
        if($Indexes){
            foreach($Indexes as $IndexPage){
                $app->match($IndexPage->getCustomurl(), '\Plugin\CustomUrlUserPage\Controller\Front\CustomUrlUserPageController::index')
                    ->value('indextype',$IndexPage->getPagecategorykey());
                    ->bind('customurluserpage_list_'.$IndexPage->getPagecategorykey());


            }

        }

        $CustomUrls = $CustomUrlUserPageRepo->findBy(array('index_flg'=>0,'del_flg'=>0));
        if($CustomUrls){
            foreach($CustomUrls as $CustomUrl){
                $app->match($CustomUrl->getCustomurl(), 'Plugin\CustomUrlUserPage\Controller\UserDataController::index')->value('route', $CustomUrl->getUserpage())->bind($CustomUrl->getBindname());

            }
        }

        /*

        // 一覧
        $app->match('/{listtype}', '\Plugin\CustomUrlUserPage\Controller\Front\CustomUrlUserPageController::index')
            ->bind('block_customurluserpage_block');


        // user定義
        $app->match('/hogehoge', 'Plugin\ShoppingEx\Controller\UserDataController::index')->value('route', 'testpage1')->bind('testpage1');
        $app->match('/aaa/hogehoge2', 'Plugin\ShoppingEx\Controller\UserDataController::index')->value('route', 'testpage2')->bind('testpage2');

        */

    }

    public function boot(BaseApplication $app)
    {
    }
}
