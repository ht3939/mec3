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

namespace Plugin\CheckedItem\Service;

use Eccube\Application;

use Doctrine\ORM\EntityManager;
use Eccube\Common\Constant;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class UtilService
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function getConfig($name)
    {
        $ret = $this->app['eccube.checkeditem.repository.config']->findOneBy(array('name' => $name));
        if($ret)return $ret->getValue();
    }
    
    function getCheckedItem($request)
    {

        $key = $this->getSaveKey();
        // Cookieがあるかチェック
        $item = $request->cookies->get($key);
        $strItems = !empty($item) ? $item : null;

        if ($strItems) {
            $Items = explode(',', $strItems);
        } else {
            $Items = array();
        }

        return $Items;
    }

    function setCheckedItem($target_product_id)
    {
        $app = $this->app;
        $request = $app['request'];
        
        $plg_path = $app['config']['root_urlpath'];

        // Cookieを上書きするため過去のCookieの読み込み
        $Items = $this->getCheckedItem($request);

        $key = $this->getSaveKey();

        // 現在の時間＋有効期限
        $expire = time() + 86400 * $this->getConfig('term');

        // 過去にCookieが発行されている場合
        if (!empty($Items)) {
            $target_key = array_search($target_product_id, $Items);
            if($target_key !== false){
                unset($Items[$target_key]);
            }
            array_unshift($Items,$target_product_id);

            $strParam = implode(',', $Items);
        } else {
            $strParam = $target_product_id;
        }

        // 一行にした配列を結合
        $response = new Response();
        $response->headers->setCookie(new Cookie($key, $strParam, $expire, $plg_path));
        $response->send();
    }
    
    public function removeCheckedItemAll()
    {
        $app = $this->app;
        
        $plg_path = $app['config']['root_urlpath'];
        
        $key = $this->getSaveKey();
        
        $expire = time() - 1800;
        
        $response = new Response();
        $response->headers->setCookie(new Cookie($key, '', $expire, $plg_path));
        $response->send();
    }
    
    private function getSaveKey()
    {
        return 'plg_CheckedItem';
    }

}
