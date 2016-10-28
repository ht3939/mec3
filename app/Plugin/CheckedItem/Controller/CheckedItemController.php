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

namespace Plugin\CheckedItem\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CheckedItemController
{
    private $app;
    
    public function index(Application $app)
    {
        $this->app = $app;
        $request = $app['request'];
        
        $Items = $app['eccube.checkeditem.service.util']->getCheckedItem($request);
        $displayNum = $app['eccube.checkeditem.service.util']->getConfig('display_num');
        $delete = $app['eccube.checkeditem.service.util']->getConfig('delete');

        $checkedItems = array();
        foreach ($Items as $Item) {

            $Product = $app['eccube.repository.product']
                ->findOneBy(
                    // 'Status'：表示状態，'del_flg'：削除フラグ
                    array('id' => $Item,
                          'Status' => 1,
                          'del_flg' => 0)
                    );

            if ($Product){
                $checkedItems[]['Product'] = $Product;
            }
        }

        $product_param = $app['eccube.recommend.service.recommend']->getProductParam($checkedItems);

        return $app->render('Block/checkeditem.twig', array(
            'CheckedItem' => array(
                'ProductList'               => $checkedItems,
                'displayNum'                => $displayNum,
                'delete'                    => $delete,
                '__EX_PRODUCT_LIST'         => $product_param['__EX_PRODUCT_LIST'],
                '__EX_PRODUCT_LIST_MAKER'   => $product_param['__EX_PRODUCT_LIST_MAKER']
            )
        ));
    }


    public function delete(Application $app)
    {
        $app['eccube.checkeditem.service.util']->removeCheckedItemAll();
        
        $request = $app['request'];
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

}
