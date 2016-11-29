<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2015 Takashi Otaki All Rights Reserved.
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\ProductClassList\ServiceProvider;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class ProductClassListServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        // Formの定義
        if (isset($app['security']) && isset($app['eccube.repository.customer_favorite_product'])) {
            $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
              $types[] = new \Plugin\ProductClassList\Form\Type\ProductClassListType($app['config'], $app['security'],        $app['eccube.repository.customer_favorite_product']);

            return $types;

            }));
        }
    }

    public function boot(BaseApplication $app)
    {
    }
}
