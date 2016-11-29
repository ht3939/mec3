<?php

/*
 * This file is part of the SEO3
 *
 * Copyright (C) 2016 Nobuhiko Kimoto
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\SEO3\ServiceProvider;

use Eccube\Application;
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Plugin\SEO3\Form\Type\SEO3Type;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;


class SEO3ServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        // プラグイン用設定画面
        $app->match('/' . $app['config']['admin_route'] . '/plugin/SEO3/config', 'Plugin\SEO3\Controller\ConfigController::index')->bind('plugin_SEO3_config');

        // 独自コントローラ
        //$app->match('/plugin/[code_name]/hello', 'Plugin\SEO3\Controller\SEO3Controller::index')->bind('plugin_SEO3_hello');

        // Form 使い方がわからん
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new SEO3Type($app);
            return $types;
        }));

        // Form Extension

        // Repository
        $app['eccube.plugin.seo3.repository.seo'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\SEO3\Entity\Seo');
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
        $app['monolog.SEO3'] = $app->share(function ($app) {

            $logger = new $app['monolog.logger.class']('plugin.SEO3');

            $file = $app['config']['root_dir'] . '/app/log/SEO3.log';
            $RotateHandler = new RotatingFileHandler($file, $app['config']['log']['max_files'], Logger::INFO);
            $RotateHandler->setFilenameFormat(
                'SEO3_{date}',
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

    }

    public function boot(BaseApplication $app)
    {
    }
}
