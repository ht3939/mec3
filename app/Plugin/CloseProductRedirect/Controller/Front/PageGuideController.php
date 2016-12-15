<?php
/*
 * Copyright(c) 2015 SystemFriend Inc. All rights reserved.
 * http://ec-cube.systemfriend.co.jp/
 */

namespace Plugin\CloseProductRedirect\Controller\Front;

use Eccube\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

class PageGuideController
{

    /**
     * ページ案内画面
     *
     * @param Application $app
     * @param Request $request
     * @return RedirectResponse
     */
    public function index(Application $app, Request $request, $redirectAction)
    {
        /* @var $Setting \Plugin\CloseProductRedirect\Service\ConfigService */
        /* @var $Util \Plugin\CloseProductRedirect\Service\UtilService */

        $this->app = $app;
        $Setting   = $app['eccube.plugin.service.cpr.config'];
        $Util      = $app['eccube.plugin.service.cpr.util'];
        $const     = $Setting->getConst();

        // 初期設定
        $tpl_subtitle = 'ページのご案内';
        $arrWord      = $Setting->getDispWord();

        // getデータチェック
        $redirect_url = $this->app['request']->get("url");
        $arrGet = array(
            'redirect_action' => $redirectAction,
            'redirect_url'    => $redirect_url,
        );

        // エラーチェック
        $error_flg = $this->checkError($arrGet);

        if (!$error_flg) {
            $url     = $redirect_url;
            $word    = $arrWord[$redirectAction];
        }else{
            $redirectAction = $const['redirect_top'];
            $url            = $app->url('homepage');
            $word           = $arrWord[$redirectAction];
        }

        // リダイレクトjavascript
        $arrPlugin = $Setting->getSetting();
        $time = $arrPlugin['wait_time'] > 0 ? $arrPlugin['wait_time'] : 5;

        // 外部内部判定
        $out_app = 0;
        if ($Util->checkInOutUrl($url)){
            $out_app = 1;
        }

        // ページ案内の表示
        return $this->app['view']->render('CloseProductRedirect/Resource/template/front/page_guide.twig',
            array(
                'tpl_subtitle' => $tpl_subtitle,
                'time'         => $time,
                'url'          => $url,
                'word'         => $word,
                'out_app'      => $out_app,
            ));
    }

    /**
     * 入力チェック
     *
     * @param $arrGet
     * @return bool
     */
    public function checkError($arrGet){
        /* @var $Setting \Plugin\CloseProductRedirect\Service\ConfigService */
        $Setting   = $this->app['eccube.plugin.service.cpr.config'];

        // redirect actionのチェック
        if (!is_numeric($arrGet['redirect_action'])){
            return false;
        }

        $arrWord = $Setting->getDispWord();
        if (!array_key_exists($arrGet['redirect_action'], $arrWord)){
            return false;
        }

        // redirect_urlのチェック 英数記号チェック
        $input_var = $arrGet['redirect_url'];
        $pattern = "/^[[:graph:][:space:]]+$/i";
        if (strlen($input_var) > 0 && !preg_match($pattern, $input_var)) {
            return false;
        }
    }
}
