<?php
/*
 * Copyright(c) 2016 SYSTEM_KD
 */

namespace Plugin\TagEx\ServiceProvider;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class TagExServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {

        // タグ登録画面
        $app->match('/' . $app["config"]["admin_route"] . '/product/tagex', '\Plugin\TagEx\Controller\Admin\Product\TagExController::index')->bind('admin_product_tag_ex');
        $app->match('/' . $app["config"]["admin_route"] . '/product/tagex/{id}/edit', '\Plugin\TagEx\Controller\Admin\Product\TagExController::index')->assert('id', '\d+')->bind('admin_product_tag_ex_edit');
        $app->delete('/' . $app["config"]["admin_route"] . '/product/tagex/{id}/delete', '\Plugin\TagEx\Controller\Admin\Product\TagExController::delete')->assert('id', '\d+')->bind('admin_product_tag_ex_delete');
        $app->post('/' . $app["config"]["admin_route"] . '/product/tagex/rank/move', '\Plugin\TagEx\Controller\Admin\Product\TagExController::moveRank')->bind('admin_product_tag_ex_rank_move');

        // タグ一括設定画面
        $app->match('/' . $app["config"]["admin_route"] . '/product/tagex/update', '\Plugin\TagEx\Controller\Admin\Product\TagExController::groupUpdate')->bind('admin_product_tag_ex_update');

        // Service 定義
        $app['tagex.service.twigrenderservice'] = $app->share(function () use ($app) {
            return new \Plugin\TagEx\Service\TwigRenderService($app);
        });

        // メニュー追加
        $app['config'] = $app->share($app->extend('config', function ($config) {

            $config['nav'][0]['child'][] = array(
                'id' => 'plg_tag_ex',
                'name' => 'タグ登録',
                'url' => 'admin_product_tag_ex',
            );

            return $config;
        }));

        $app['tagex.repository.tagex'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\TagEx\\Entity\TagEx');
        });

        // FormType
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Plugin\TagEx\Form\Type\Admin\TagExType($app['config']);
            $types[] = new \Plugin\TagEx\Form\Type\Admin\UpdateTagType($app['config']);
            return $types;
        }));
    }

    public function boot(BaseApplication $app)
    {
    }
}
