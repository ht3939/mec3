<?php

/*
 * This file is part of the CRM3
 *
 * Copyright (C) 2016 Nobuhiko Kimoto
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CRM3\ServiceProvider;

use Eccube\Application;
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Plugin\CRM3\Form\Type\CRM3ConfigType;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;


class CRM3ServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        $admin = $app['config']['admin_route'];
        $app->match($admin . '/crm', '\\Plugin\\CRM3\\Controller\\CRM3Controller::index')
            ->bind('admin_crm');

        // プラグイン用設定画面
        //$app->match('/' . $app['config']['admin_route'] . '/plugin/CRM3/config', 'Plugin\CRM3\Controller\ConfigController::index')->bind('plugin_CRM3_config');

        // 独自コントローラ
        //$app->match('/plugin/[code_name]/hello', 'Plugin\CRM3\Controller\CRM3Controller::index')->bind('plugin_CRM3_hello');

        // Form
        /*$app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new CRM3ConfigType($app);
            return $types;
        }));*/

        // Form Extension

        // Repository
        $app['eccube.plugin.crm3.repository.contact'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\CRM3\Entity\Contact');
        });

        // Service

        // // メッセージ登録
        // $app['translator'] = $app->share($app->extend('translator', function ($translator, \Silex\Application $app) {
        //     $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
        //     $file = __DIR__ . '/../Resource/locale/message.' . $app['locale'] . '.yml';
        //     if (file_exists($file)) {
        //         $translator->addResource('yaml', $file, $app['locale']);
        //     }
        //     return $translator;
        // }));

        // load config
        // $conf = $app['config'];
        // $app['config'] = $app->share(function () use ($conf) {
        //     $confarray = array();
        //     $path_file = __DIR__ . '/../Resource/config/path.yml';
        //     if (file_exists($path_file)) {
        //         $config_yml = Yaml::parse(file_get_contents($path_file));
        //         if (isset($config_yml)) {
        //             $confarray = array_replace_recursive($confarray, $config_yml);
        //         }
        //     }

        //     $constant_file = __DIR__ . '/../Resource/config/constant.yml';
        //     if (file_exists($constant_file)) {
        //         $config_yml = Yaml::parse(file_get_contents($constant_file));
        //         if (isset($config_yml)) {
        //             $confarray = array_replace_recursive($confarray, $config_yml);
        //         }
        //     }

        //     return array_replace_recursive($conf, $confarray);
        // });

        // ログファイル設定
        $app['monolog.CRM3'] = $app->share(function ($app) {

            $logger = new $app['monolog.logger.class']('plugin.CRM3');

            $file = $app['config']['root_dir'] . '/app/log/CRM3.log';
            $RotateHandler = new RotatingFileHandler($file, $app['config']['log']['max_files'], Logger::INFO);
            $RotateHandler->setFilenameFormat(
                'CRM3_{date}',
                'Y-m-d'
            );

            $logger->pushHandler(
                new FingersCrossedHandler(
                    $RotateHandler,
                    new ErrorLevelActivationStrategy(Logger::INFO)
                )
            );

            return $logger;
        });

        // サブナビの拡張
        $app['config'] = $app->share($app->extend('config', function ($config) {
            $nav = array(
                'id' => 'admin_crm',
                'name' => 'お問い合わせ管理',
                'has_child' => 'true',
                'icon' => 'cb-users',
                'child' => array(
                    array(
                        'id' => 'admin_crm',
                        'url' => 'admin_crm',
                        'name' => 'お問い合わせ一覧',
                    ),
                ),
            );

            $config['nav'][] = $nav;
            return $config;
        }));

    }

    public function boot(BaseApplication $app)
    {
    }
}
