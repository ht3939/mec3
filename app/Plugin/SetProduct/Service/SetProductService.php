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

namespace Plugin\SetProduct\Service;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Event\EventArgs;

class SetProductService
{
	/** @var \Eccube\Application */
	public $app;

	/** @var \Eccube\Entity\BaseInfo */
	public $BaseInfo;

	public $SETPRODUCTKEY = 'eccube.plugin.setproduct';

	/**
	 * コンストラクタ
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		$this->app = $app;
		$this->BaseInfo = $app['eccube.repository.base_info']->get();
	}

	/**
	 * セット商品情報用商品IDセット
	 * @param $event
	 * @return null
	 */
	public function SetShopingProductSetProduct(EventArgs $event) {
        $request = $event->getRequest();
        $session = $request->getSession();

        $product_id_arr = array();
        $Order = $event->getArgument('Order');
        foreach($Order->getOrderDetails() as $key => $val){
            $product_id_arr[] = $val->getProduct()->getId();
        }

        $session->set($this->SETPRODUCTKEY,$product_id_arr);
	}

	/**
	 * セット商品情報取得
	 * @param $event
	 * @return $data
	 */
	public function GetShopingProductSetProduct(EventArgs $event) {
		$session = $event->getRequest()->getSession();
        $product_id_arr = $session->get($this->SETPRODUCTKEY);

        if(!$product_id_arr){
        	return null;
        }

        $setproduct_arr = array();
        foreach($product_id_arr as $key => $val){
			$setproduct_arr[] = $this->app['eccube.plugin.setproduct.repository.product_setproduct']->find($val);
        }

		return $setproduct_arr;
	}

	/**
	 * セット商品情報からSIMフラグが有効なメーカー取得
	 * @param $event
	 * @return $data
	 */
	public function GetShopingProductSetProductSimMaker(EventArgs $event) {
		$setproduct_arr = $this->GetShopingProductSetProduct($event);

		$maker_id_arr = array();
		foreach ($setproduct_arr as $key => $val) {
			if($val['setproduct_sim_flg']==1){
				$maker_id_arr[] = $val['Maker']->getId();
			}
		}

		return $maker_id_arr;
	}


}
