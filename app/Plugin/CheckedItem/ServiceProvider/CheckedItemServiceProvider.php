<?php
/*
* Plugin Name : CheckedItem
*
* Copyright (C) 2015 BraTech Co., Ltd. All Rights Reserved.
* http://www.bratech.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\CheckedItem\ServiceProvider;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class CheckedItemServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {

    	// Routing
        $app->match('/' . $app['config']['admin_route'] . '/plugin/checkeditem/config','Plugin\CheckedItem\Controller\Admin\ConfigController::index')->bind('plugin_CheckedItem_config');
    	$app->match('/block/checkeditem', '\Plugin\\CheckedItem\Controller\CheckedItemController::index')->bind('block_checkeditem');
        $app->match('/block/checkeditem/delete', '\Plugin\\CheckedItem\Controller\CheckedItemController::delete')->bind('block_checkeditem_delete');

        // Form/Type
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use($app) {
            $types[] = new \Plugin\CheckedItem\Form\Type\Admin\ConfigType($app);
            return $types;
        }));
        
        // Repositoy
        $app['eccube.checkeditem.repository.config'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\CheckedItem\Entity\Config');
        });
        
        $app['eccube.checkeditem.service.util'] = $app->share(function () use ($app) {
            return new \Plugin\CheckedItem\Service\UtilService($app);
        });
        
        // locale message
        $app['translator'] = $app->share($app->extend('translator', function ($translator, \Silex\Application $app) {
            $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());

            $file = __DIR__ . '/../Resource/locale/message.' . $app['locale'] . '.yml';
            if (file_exists($file)) {
                $translator->addResource('yaml', $file, $app['locale']);
            }

            return $translator;
        }));
    }

    public function boot(BaseApplication $app)
    {
    }
}
