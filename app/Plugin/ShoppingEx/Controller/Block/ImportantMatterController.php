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

namespace Plugin\ShoppingEx\Controller\Block;

use Eccube\Application;

class ImportantMatterController
{
    const SHOPPINGEX_SESSON_ORDER_KEY = 'eccube.plugin.shoppingex.order.key';

    /**
     * @param Application $app
     */
    public function index(Application $app)
    {
        //dump($app);
        $req = $app['request'];
        //注文情報の取得
        $Order = $req->getSession()->get(self::SHOPPINGEX_SESSON_ORDER_KEY);
        // array(
        //     'hasPayMonthly'=>$this->hasPayMonthly,
        //     'hasSimOrder'=>$hasSimOrder,
        //     'Order'=>$Order,
        //     'OrderMaker'=>null
        //     ));
        $importantmattermakers = explode(',',$app['config']['shoppingex_important_matter_disp_maker']);
        $important_matter_disp = false;
        foreach($importantmattermakers as $mk){
            if(in_array($mk,$Order['OrderMaker'])){
                $important_matter_disp=true;
            }
        }

        //SIMの重要説明事項の表示制御用
        //いったんSIM提供側固有の表記は不要に。
        return $app['view']->render('Block/important_matter_block.twig', array(
            'Order' => null
            ,'ShowImportantMatter' => $important_matter_disp
        ));
    }
}
