<?php
/*
 * Copyright(c) 2016 SYSTEM_KD
 */


namespace Plugin\SimpleStateChange\ServiceProvider;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class SimpleStateChangeServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        // Controller
        $app->match('/' . $app["config"]["admin_route"] . '/product/ssc', '\\Plugin\\SimpleStateChange\\Controller\\Admin\\SimpleStateChangeController::index')->bind('plg_ssc');
        $app->match('/' . $app["config"]["admin_route"] . '/product/ssc_update', '\\Plugin\\SimpleStateChange\\Controller\\Admin\\SimpleStateChangeController::statusUpdate')->bind('plg_ssc_update');

        // Service 定義
        $app['ssc.service.twigrenderservice'] = $app->share(function () use ($app) {
            return new \Plugin\SimpleStateChange\Service\TwigRenderService($app);
        });

        // FormType
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Plugin\SimpleStateChange\Form\Type\Admin\UpdateStatusType($app['config']);
            return $types;
        }));
    }

    public function boot(BaseApplication $app)
    {
    }
}
