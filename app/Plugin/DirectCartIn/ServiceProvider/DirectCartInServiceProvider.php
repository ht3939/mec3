<?php
/*
 * Copyright(c) 2016 SYSTEM_KD
 */

namespace Plugin\DirectCartIn\ServiceProvider;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class DirectCartInServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {

        // Controller
        $app->match('/products/direct_cartin', '\Plugin\DirectCartIn\Controller\ProductClassViewController::view')->bind('plg_direct_cartin');

        // Service
        $app['directcartin.service.twigrenderservice'] = $app->share(function () use ($app) {
            return new \Plugin\DirectCartIn\Service\TwigRenderService($app);
        });

    }

    public function boot(BaseApplication $app)
    {
    }
}
