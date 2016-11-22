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
use Eccube\Entity\Order;
use Plugin\ShoppingEx\Entity\ShoppingEx;


class ShoppingExService
{
    /** @var \Eccube\Application */
    public $app;

    /** @var \Eccube\Entity\BaseInfo */
    public $BaseInfo;
    
    public $redirectTo;

    /**
     * コンストラクタ
     * @param Application $app
     */
    public function __construct(Application $app)
    {

        $this->app = $app;
        $this->BaseInfo = $app['eccube.repository.base_info']->get();
    }
    public function setRedirectTo($arr){
        $this->redirectTo = $arr;
    }
    public function getRedirectTo(){
        return $this->redirectTo;
    }
    public function getCurrOrder(){

    }
    public function setCurrOrder($Order)
    {
        //$this->app->get
    }

    public function sendShoppingOrder(EventArgs $event){
        $req = $event->getRequest();
        $app = $this->app;
        $Order = $event->getArgument('Order');
        $type ="Web完結";



        $route = $app['eccube.plugin.fdroute.service.fdroute']->getStoredFdRoute();
        $note = '';
        $note = $app['eccube.plugin.fdroute.service.fdroute']->getStoredFdRouteNote();
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
        $req = $event->getRequest();
        $app = $this->app;
        $type ="問合";
        $Contact = $event->getArgument('data');

        $route = $app['eccube.plugin.fdroute.service.fdroute']->getStoredFdRoute();

        $data = array(
                    "_Route"             => $route['route_name'].$type,
                    "_MainProgress"      => "未処理",
                    "_Name"              => $Contact['name01'].$Contact['name02'],
                    "_Kana"              => $Contact['kana01'].$Contact['kana02'],
                    "_Year_Birth_Day"    => "",
                    "_Gender"            => "",
                    "_Zip"               => $Contact['zip01'].$Contact['zip02'],
                    "_Add1"              => $Contact['pref']->getName().$Contact['addr01'],
                    "_Add2"              => $Contact['addr02'],
                    "_Add3"              => "",
                    "_Tel"               => $Contact['tel01'].$Contact['tel02'].$Contact['tel03'],
                    "_Mail"              => $Contact['email'],
                    "_Password"          => "",
                    "_Message"           => $Contact['contents'],
                    );

        $app['eccube.plugin.kintonetransadmin.service.kintonetransadmin']
            ->sendKintone($req,
                array(
                    'DataValues'=> $data)
                );


    }

    public function cleanupShoppingOrder(EventArgs $event){
        $req = $event->getRequest();
        $app = $this->app;
        $Order = $event->getArgument('Order');

        //最後のオーダーから１週間過ぎたものは、個人情報消す


        //
        //SELECT `order_id`, `customer_id`, `order_country_id`
                //, `order_pref`, `order_sex`, `order_job`, `payment_id`
                //, `device_type_id`, `pre_order_id`, `message`
                //, `order_name01`, `order_name02`, `order_kana01`
                //, `order_kana02`, `order_company_name`
                //, `order_email`, `order_tel01`, `order_tel02`, `order_tel03`
                //, `order_fax01`, `order_fax02`, `order_fax03`
                //, `order_zip01`, `order_zip02`, `order_zipcode`
                //, `order_addr01`, `order_addr02`, `order_birth`
                //, `subtotal`, `discount`, `delivery_fee_total`, `charge`, `tax`, `total`
                //, `payment_total`, `payment_method`, `note`, `create_date`, `update_date`, `order_date`, `commit_date`, `payment_date`, `del_flg`, `status` 
        //FROM `mecq3dev`.`dtb_order`;
        //

        $now = new \Datetime();
        $interval = \DateInterval::createfromdatestring('-1 week');

        $lastweek = $now->add($interval);

        $query = $app['eccube.repository.order']->createQueryBuilder('p')
            ->where("p.create_date < :oneweek")
            ->setParameter('oneweek',$lastweek)
            ->getQuery();

        $oldOrder = array();
        $oldOrder = $query->getResult();


        foreach($oldOrder as $order){
            //$order = $this->app['eccube.repository.order']->find($order->getId());
            $orderid = $order->getId();
            $shoppingex = $this->app['shoppingex.repository.shoppingex']->find($orderid);
            $this->cleanupOrderInfo($order,$shoppingex);

        }



    }
    private function cleanupOrderInfo($Order,$ShoppingEx)
    {
        $app = $this->app;

        if($ShoppingEx){
            $ShoppingEx
                ->setCardno1('****')
                ->setCardno2('****')
                ->setCardno3('****')
                ->setCardno4('****')
                ->setHolder('***')
                //->setCardtype()
                ->setLimitmon('1')
                ->setLimityear('2016')
                ->setCardsec('***');

            $app['orm.em']->persist($ShoppingEx);
            $app['orm.em']->flush();

            //$app['orm.em']->persist($shoppingex);


        }
        if($Order){
            $Order
                ->setName01('***')
                ->setName02('***')
                ->setKana01('***')
                ->setKana02('***')
                ->setCompanyName('***')
                ->setEmail('***')
                ->setTel01('0000')
                ->setTel02('0000')
                ->setTel03('0000')
                ->setFax01('0000')
                ->setFax02('0000')
                ->setFax03('0000')
                ->setZip01('000')
                ->setZip02('0000')
                //->setZipCode('000'.'0000')
                //->setPref($Customer->getPref())
                ->setAddr01('****')
                ->setAddr02('***')
                ->setBirth(null);

            $app['orm.em']->persist($Order);
            $app['orm.em']->flush();



        }

        //return $Order;
    }

}
