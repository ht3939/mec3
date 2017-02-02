<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2000-2016 LOCKON CO.,LTD. All Rights Reserved.
* http://www.lockon.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
namespace Plugin\DSContents\ServiceProvider;

use Eccube\Application;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Processor\WebProcessor;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Bridge\Monolog\Logger;

/**
 * Class DSContentsServiceProvider
 * @package Plugin\DSContents\ServiceProvider
 */
class DSContentsServiceProvider implements ServiceProviderInterface
{
    /**
     * サービス登録処理
     * @param BaseApplication $app
     */
    public function register(BaseApplication $app)
    {
        /**
         * ルーティング登録
         * 管理画面 > 設定 > 基本情報設定 > ＤＳコンテンツ商品基本情報設定画面
         */
        $app->match(
            '/'.$app['config']['admin_route'].'/DSContents/setting',
            'Plugin\DSContents\Controller\AdminDSContentsController::index'
        )->bind('DSContents_info');


        /**
         * ルーティング登録
         * 管理画面 > 商品一覧 > メニュー > ＤＳコンテンツ商品管理
         */


        /**
         * ルーティング登録
         * 管理画面 > 受注一覧 >　メニュー > ＤＳコンテンツ商品リンク管理
         */


        /**
         * ルーティング登録
         * Mypage >　注文履歴 >  ＤＳコンテンツ商品リンク
         */

 

        /**
         * レポジトリ登録
         */
        $app['eccube.plugin.DSContents.repository.DSContents'] = $app->share(
            function () use ($app) {
                return $app['orm.em']->getRepository('Plugin\DSContents\Entity\DSContents');
            }
        );



        // サービスの登録
        $app['eccube.plugin.DSContents.service.DSContents'] = $app->share(function () use ($app) {
            return new \Plugin\DSContents\Service\DSContentsService($app);
        });

        /**
         * フォームタイプ登録
         */
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {

            return $types;
        })
        );

        /**
         * メニュー登録
         */
        $app['config'] = $app->share(
            $app->extend(
                'config',
                function ($config) {
                    $addNavi['id'] = "DSContents_info";
                    $addNavi['name'] = "DSContents設定";
                    $addNavi['url'] = "DSContents_info";
                    $nav = $config['nav'];
                    foreach ($nav as $key => $val) {
                        if ("setting" == $val["id"]) {
                            $nav[$key]['child'][0]['child'][] = $addNavi;
                        }
                    }
                    $config['nav'] = $nav;

                    return $config;
                }
            )
        );


        /**
         * メッセージ登録
         */
        $app['translator'] = $app->share(
            $app->extend(
                'translator',
                function ($translator, \Silex\Application $app) {
                    $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
                    $file = __DIR__.'/../Resource/locale/message.'.$app['locale'].'.yml';
                    if (file_exists($file)) {
                        $translator->addResource('yaml', $file, $app['locale']);
                    }

                    return $translator;
                }
            )
        );

        // ログファイル設定
        $app['monolog.DSContents'] = $this->initLogger($app, 'DSContents');

        // ログファイル管理画面用設定
        $app['monolog.DSContents.admin'] = $this->initLogger($app, 'DSContents_admin');

    }

    /**
     * 初期化時処理
     *  - 本クラスでは使用せず
     * @param BaseApplication $app
     */
    public function boot(BaseApplication $app)
    {
    }

    /**
     * ＤＳコンテンツ商品プラグイン用ログファイルの初期設定
     *
     * @param BaseApplication $app
     * @param $logFileName
     * @return \Closure
     */
    protected function initLogger(BaseApplication $app, $logFileName)
    {

        return $app->share(function ($app) use ($logFileName) {
            $logger = new $app['monolog.logger.class']('plugin.DSContents');
            $file = $app['config']['root_dir'].'/app/log/'.$logFileName.'.log';
            $RotateHandler = new RotatingFileHandler($file, $app['config']['log']['max_files'], Logger::INFO);
            $RotateHandler->setFilenameFormat(
                $logFileName.'_{date}',
                'Y-m-d'
            );

            $token = substr($app['session']->getId(), 0, 8);
            $format = "[%datetime%] [".$token."] %channel%.%level_name%: %message% %context% %extra%\n";
            // $RotateHandler->setFormatter(new LineFormatter($format, null, false, true));
            $RotateHandler->setFormatter(new LineFormatter($format));

            $logger->pushHandler(
                new FingersCrossedHandler(
                    $RotateHandler,
                    new ErrorLevelActivationStrategy(Logger::INFO)
                )
            );

            $logger->pushProcessor(function ($record) {
                // 出力ログからファイル名を削除し、lineを最終項目にセットしなおす
                unset($record['extra']['file']);
                $line = $record['extra']['line'];
                unset($record['extra']['line']);
                $record['extra']['line'] = $line;

                return $record;
            });

            $ip = new IntrospectionProcessor();
            $logger->pushProcessor($ip);

            $web = new WebProcessor();
            $logger->pushProcessor($web);

            // $uid = new UidProcessor(8);
            // $logger->pushProcessor($uid);

            $process = new ProcessIdProcessor();
            $logger->pushProcessor($process);


            return $logger;
        });

    }


}
