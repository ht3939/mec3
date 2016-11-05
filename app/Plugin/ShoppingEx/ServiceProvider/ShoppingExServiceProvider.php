<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
* http://www.lockon.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\ShoppingEx\ServiceProvider;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class ShoppingExServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        //$c->match('/help/tradelaw', '\Eccube\Controller\HelpController::tradelaw')->bind('help_tradelaw');
        $app->match('/help/company' , '\Eccube\Controller\HelpController::tradelaw')->bind('help_tradelaw');
        $app->match('/help/about' , 'Plugin\ShoppingEx\Controller\RedirectController::index')->bind('help_about');
        $app->match('/help/guide' , 'Plugin\ShoppingEx\Controller\RedirectController::index')->bind('help_guide_404');

        $app->match('/about-sim/' , 'Plugin\ShoppingEx\Controller\AboutSimController::index')->bind('about-sim');
        //$c->match('/about-sim', '\Eccube\Controller\UserDataController::index')->bind('aboutsim');

        $app->match('/guide' , '\Eccube\Controller\HelpController::guide')->bind('help_guide');

        // Form/Type
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use($app) {
            $types[] = new \Plugin\ShoppingEx\Form\Type\CardNoType($app['config']);
            $types[] = new \Plugin\ShoppingEx\Form\Type\CardLimitType($app['config']);
            $types[] = new \Plugin\ShoppingEx\Form\Type\CardTypeType($app['config']);
            $types[] = new \Plugin\ShoppingEx\Form\Type\CardSecType($app['config']);
            $types[] = new \Plugin\ShoppingEx\Form\Type\CardFormType($app);
            return $types;
        }));


        // Form/Extension
        $app['form.type.extensions'] = $app->share($app->extend('form.type.extensions', function ($extensions) {
            $extensions[] = new \Plugin\ShoppingEx\Form\Extension\ShoppingExExtension();
            return $extensions;
        }));

        //Repository
        $app['shoppingex.repository.shoppingex'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\ShoppingEx\Entity\ShoppingEx');
        });

        // サービスの登録
        $app['eccube.plugin.shoppingex.service.shoppingex'] = $app->share(function () use ($app) {
            return new \Plugin\ShoppingEx\Service\ShoppingExService($app);
        });
    }

    public function boot(BaseApplication $app)
    {
    }
}
