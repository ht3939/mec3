<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Plugin\ShoppingEx\Service;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Event\EventArgs;

class ShoppingExService
{
    /** @var \Eccube\Application */
    public $app;

    /** @var \Eccube\Entity\BaseInfo */
    public $BaseInfo;

    /**
     * コンストラクタ
     * @param Application $app
     */
    public function __construct(Application $app)
    {

        $this->app = $app;
        $this->BaseInfo = $app['eccube.repository.base_info']->get();
    }
    public function sendShoppingOrder(EventArgs $event){
        $req = $event->getRequest();
        $app = $this->app;
        $Order = $event->getArgument('Order');
        $type ="Web完結";



        $route = $app['eccube.plugin.fdroute.service.fdroute']->getStoredFdRoute();
        $note = 'dummy note(A pattern)';


        $orderDetails = $Order->getOrderDetails();
        $plgOrderDetails = $app['eccube.productoption.service.util']->getPlgOrderDetails($orderDetails);
        
        $Shippings = $Order->getShippings();
        $plgShipmentItems = $app['eccube.productoption.service.util']->getPlgShipmentItems($Shippings);
        
        $extendmsg = 
            $app->renderView('Mail/order_kintone.twig', array(
                'header' => null,
                'footer' => null,
                'route_note'=> $route['route_name'].$type.$note,
                'message'=>$Order->getMessage(),
                'Order' => $Order,
                'plgOrderDetails' => $plgOrderDetails,
                'plgShipmentItems' => $plgShipmentItems,
            ));


        // dump('get route');
        // dump($route);
        $form = $event->getArgument('form');
        $card = $event->getArgument('CardInfo');
        $CardInfo = $card;


        $data = array(
                    "_Route"             => $route['route_name'].$type,
                    "_MainProgress"      => "未処理",
                    "_Name"              => $Order->getName01().$Order->getName02(),
                    "_Kana"              => $Order->getKana01().$Order->getKana02(),
                    "_Year_Birth_Day"    => "",
                    "_Gender"            => $Order->getSex()->getName(),
                    "_Zip"               => $Order->getZip01().$Order->getZip02(),
                    "_Add1"              => $Order->getPref().$Order->getAddr01(),
                    "_Add2"              => $Order->getAddr02(),
                    "_Add3"              => "",
                    "_Tel"               => $Order->getTel01().$Order->getTel02().$Order->getTel03(),
                    "_Mail"              => $Order->getEmail(),
                    "_Card_Num"          => is_array($card)?$CardInfo['cardno']:"" ,
                    "_Card_Name"         => is_array($card)?$CardInfo['cardholder']:"" ,
                    "_Card_Type"         => is_array($card)?$CardInfo['cardtype']:"" ,
                    "_Card_Limit_Year"   => is_array($card)?$CardInfo['cardlimitmon']:"" ,
                    "_Card_Limit_Month"  => is_array($card)?$CardInfo['cardlimityear']:"" ,
                    "_card_cord"         => is_array($card)?$CardInfo['cardsec']:"" ,
                    "_Password"          => "",
                    "_Message"           => $extendmsg,
                    "_CmsOrderId"        => $Order->getId()
                    );

        $app['eccube.plugin.kintonetransadmin.service.kintonetransadmin']
            ->sendKintone($req,
                array('Order'=>$Order,
                    'Route'=>$route,
                    'Note'=>$note,
                    'DataValues'=> $data)
                );


    }
    public function sendContact(EventArgs $event){
        $app = $this->app;
        $type ="問合";

        $route = $app['eccube.plugin.fdroute.service.fdroute']->getStoredFdRoute();

        $extendmsg = 
            $app->renderView('Mail/order_kintone.twig', array(
                'header' => null,
                'footer' => null,
                'route_note'=> $route['route_name'].$type.$note,
                'message'=>$Order->getMessage(),
            ));


        $data = array(
                    "_Route"             => $route['route_name'].$type,
                    "_MainProgress"      => "未処理",
                    "_Name"              => $Order->getName01().$Order->getName02(),
                    "_Kana"              => $Order->getKana01().$Order->getKana02(),
                    "_Year_Birth_Day"    => "",
                    "_Gender"            => $Order->getSex()->getName(),
                    "_Zip"               => $Order->getZip01().$Order->getZip02(),
                    "_Add1"              => $Order->getPref().$Order->getAddr01(),
                    "_Add2"              => $Order->getAddr02(),
                    "_Add3"              => "",
                    "_Tel"               => $Order->getTel01().$Order->getTel02().$Order->getTel03(),
                    "_Mail"              => $Order->getEmail(),
                    "_Password"          => "",
                    "_Message"           => $extendmsg,
                    );

        $app['eccube.plugin.kintonetransadmin.service.kintonetransadmin']
            ->sendKintone($req,
                array(
                    'DataValues'=> $data)
                );


    }


}
