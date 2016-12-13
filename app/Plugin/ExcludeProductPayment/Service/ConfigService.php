<?php
/*
 * Copyright(c) 2015 SystemFriend Inc. All rights reserved.
 * http://ec-cube.systemfriend.co.jp/
*/
namespace Plugin\ExcludeProductPayment\Service;

use Eccube\Util\EntityUtil;

class ConfigService
{

    protected $app;
    protected $const;
    protected $Settings;

    public $pluginName;
    public $pluginCode;
    public $pluginVersion;

    public $pluginPath;

    /**
     * コンストラクタ
     *
     * @param  \Eccube\Application $app
     * @access public
     */
    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
        $this->const = $this->app['config']['ExcludeProductPayment']['const'];

        $this->pluginName    = $this->const['name'];
        $this->pluginCode    = $this->const['code'];
        $this->pluginVersion = $this->const['version'];

        $this->pluginPath    = $app['config']['plugin_realdir'] . '/' . $this->pluginCode;

        $this->init();
    }

    /**
     * 初期化処理
     *
     * @access public
     * @return void
     */
    public function init()
    {
        $this->Settings = $this->getSubData();

        foreach ((array)$this->Settings as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     * プラグインの設定ファイル
     *
     * @return array()
     */
    public function getConst()
    {
        return $this->const;
    }

    /**
     * リダイレクト一覧の取得
     *
     * @param bool $config_flg true 設定画面、false 一覧
     * @return array リダイレクト先 => 名称
     */
    public function getRedirectPages($config_flg = true)
    {
        $const = $this->const;
        if ($config_flg) {
            $pages = array(
                $const['redirect_non'] => '指定無し',
                $const['redirect_top'] => 'TOPページ',
                $const['redirect_url'] => '指定URL',
            );
        }else{
            $pages = array(
                $const['redirect_id']  => '商品ID',
                $const['redirect_url'] => 'URL',
                $const['redirect_non'] => '指定無し',
            );
        }

        return $pages;
    }

    /**
     * 支払方法の取得
     *
     * @param bool $config_flg true 設定画面、false 一覧
     * @return array リダイレクト先 => 名称
     */
    public function getExcludePayment($config_flg = true)
    {
        $app = $this->app;

        $PaymentRepo = $app['eccube.repository.payment'];

        $const = $this->const;
        if ($config_flg) {
            $pages = array(
                $const['redirect_non'] => '指定無し',
                $const['redirect_top'] => 'TOPページ',
                $const['redirect_url'] => '指定URL',
            );
        }else{
            $pages = array(
                $const['redirect_id']  => '商品ID',
                $const['redirect_url'] => 'URL',
                $const['redirect_non'] => '指定無し',
            );

        }

        $pages = array();
        foreach($PaymentRepo->findAllArray() as $k=>$v){
            $pages[$k] = $v['method'];
        }

        return $pages;
    }
    /**
     * ページ案内で表示する文言
     *
     * @return array
     */
    public function getDispWord(){
        $const = $this->const;

        $arrWord = array(
            $const['redirect_top'] => 'TOPページ',
            $const['redirect_id']  => '関連商品ページ',
            $const['redirect_url'] => '関連ページ',
        );

        return $arrWord;
    }

    /**
     * 設定値を一括取得
     *
     * @return array 設定値配列
     */
    public function getSetting()
    {
        return $this->Settings;
    }

    /**
     * 設定値を取得
     *
     * @param string $name
     * @return array 設定値
     */
    public function get($name)
    {
        if (isset($this->Settings[$name]) == false) {
            return null;
        }
        return $this->Settings[$name];
    }

    /**
     * サブデータの取得
     *
     * @return array|null
     */
    public function getSubData()
    {
        if (isset($this->subData)) {
            return $this->subData;
        }

        /* @var $CprPlugin \Plugin\ExcludeProductPayment\Repository\CprPluginRepository */
        $CprPlugin = $this->app['eccube.plugin.repository.epp.plugin'];
        $ret = $CprPlugin->getSubData($this->pluginCode);

        if (isset($ret)) {
            $this->subData = unserialize($ret);

            return $this->subData;
        }
        return null;
    }

    /**
     * サブデータをDBへ登録する
     *
     * @param array $formData
     */
    function registerSubData($formData)
    {
        $pluginCode = $this->pluginCode;

        /* @var $CprPlugin \Plugin\ExcludeProductPayment\Entity\CprPlugin */
        $CppPlugin = $this->app['eccube.plugin.repository.epp.plugin']->findOneBy(array('code' => $pluginCode));

        $subData = serialize($formData);

        if (!is_null($CppPlugin)) {
            $CprPlugin->setSubData($subData);
            $this->app['orm.em']->persist($CprPlugin);
            $this->app['orm.em']->flush();
        }
    }

    /**
     * プラグイン設定で登録されたURLの取得
     *
     * @return string
     */
    public function getConfigUrl(){
        $url = '';
        $const = $this->const;

        $redirect_action = $this->Settings['redirect_select'];

        switch ($redirect_action){
            case $const['redirect_top']: $url = $this->app->url('homepage');      break;
            case $const['redirect_url']: $url = $this->Settings['redirect_url']; break;
            case $const['redirect_non']:
            default:
                break;
        }
        return array($url, $redirect_action);
    }

}
