<?php
/*
 * Copyright(c) 2016 SYSTEM_KD
 */

namespace Plugin\AdminProductListPlus\ServiceProvider;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class AdminProductListPlusServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        // Service 定義
        $app['adminproductlistplus.service.twigrenderservice'] = $app->share(function () use ($app) {
            return new \Plugin\AdminProductListPlus\Service\TwigRenderService($app);
        });
    }

    public function boot(BaseApplication $app)
    {
    }
}
