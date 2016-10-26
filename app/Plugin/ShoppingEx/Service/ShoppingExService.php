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
        $Order = $event->getArgument('Order');

        $app = $this->app;
        $route = $app['eccube.plugin.fdroute.service.fdroute']->getStoredFdRoute();
        $note = null;
dump('get route');
dump($route);

        $data = array(
                    "Route"             => array("value" => $route['route_name'].$type),
                    "MainProgress"      => array("value" => "未処理"),
                    "Name"              => array("value" => $Order->getName01().$Order->getName02()),
                    "Kana"              => array("value" => $Order->getKana01().$Order->getKana02()),
                    "Year_Birth_Day"    => array("value" => ""),
                    "Gender"            => array("value" => $Order->getSex()->getName()),
                    "Zip"               => array("value" => $Order->getZip01().$Order->getZip02()),
                    "Add1"              => array("value" => $Order->getPref().$Order->getAddr01()),
                    "Add2"              => array("value" => $Order->getAddr02()),
                    "Add3"              => array("value" => ""),
                    "Tel"               => array("value" => $Order->getTel01().$Order->getTel02().$Order->getTel03()),
                    "Mail"              => array("value" => $Order->getEmail()),
                    "Card_Num"          => array("value" => $i_card),
                    "Card_Name"         => array("value" => $i_holder),
                    "Card_Type"         => array("value" => $i_credit),
                    "Card_Limit_Year"   => array("value" => $i_limit_year),
                    "Card_Limit_Month"  => array("value" => sprintf("%02d", $i_limit_month)),
                    "card_cord"         => array("value" => $i_code),
                    "Password"          => array("value" => ""),
                    "Message"           => array("value" => $Order->getMessage())
                    );

        $app['eccube.plugin.kintonetransadmin.service.kintonetransadmin']->sendKintone($req,
            array('Order'=>$Order,
                'Route'=>$route,
                'Note'=>$note
                'Kintone'=> $data)
            );


    }


}
