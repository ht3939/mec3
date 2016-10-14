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

namespace Plugin\FdRoute\Service;

use Eccube\Application;
use Eccube\Common\Constant;

class FdRouteService
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

	/**
	 * おすすめ商品情報を新規登録する
	 * @param $data
	 * @return bool
	 */
	public function createFdRoute($data) {
		// おすすめ商品詳細情報を生成する
		$FdRoute = $this->newFdRoute($data);

		$em = $this->app['orm.em'];

		// おすすめ商品情報を登録する
		$em->persist($FdRoute);

		$em->flush();

		return true;
	}

	/**
	 * おすすめ商品情報を更新する
	 * @param $data
	 * @return bool
	 */
	public function updateFdRoute($data) {
		$dateTime = new \DateTime();
		$em = $this->app['orm.em'];

		// おすすめ商品情報を取得する
		$FdRoute =$this->app['eccube.plugin.fdroute.repository.fdroute_product']->find($data['id']);
		if(is_null($FdRoute)) {
			false;
		}

		// おすすめ商品情報を書き換える
		$FdRoute->setConditions($data['conditions']);
		$FdRoute->setRouteString($data['route_string']);
		$FdRoute->setRouteStringPos($data['route_string_pos']);
		$FdRoute->setFdString($data['fd_string']);
		$FdRoute->setUpdateDate($dateTime);

		// おすすめ商品情報を更新する
		$em->persist($FdRoute);

		$em->flush();

		return true;
	}

	/**
	 * おすすめ商品情報を削除する
	 * @param $fdrouteId
	 * @return bool
	 */
	public function deleteFdRoute($fdrouteId) {
		$currentDateTime = new \DateTime();
		$em = $this->app['orm.em'];

		// おすすめ商品情報を取得する
		$FdRoute =$this->app['eccube.plugin.fdroute.repository.fdroute_product']->find($fdrouteId);
		if(is_null($FdRoute)) {
			false;
		}
		// おすすめ商品情報を書き換える
		$FdRoute->setDelFlg(Constant::ENABLED);
		$FdRoute->setUpdateDate($currentDateTime);

		// おすすめ商品情報を登録する
		$em->persist($FdRoute);

		$em->flush();

		return true;
	}

	/**
	 * おすすめ商品情報の順位を上げる
	 * @param $fdrouteId
	 * @return bool
	 */
	public function rankUp($fdrouteId) {
		$currentDateTime = new \DateTime();
		$em = $this->app['orm.em'];

		// おすすめ商品情報を取得する
		$FdRoute =$this->app['eccube.plugin.fdroute.repository.fdroute_product']->find($fdrouteId);
		if(is_null($FdRoute)) {
			false;
		}
		// 対象ランクの上に位置するおすすめ商品を取得する
		$TargetFdRoute =$this->app['eccube.plugin.fdroute.repository.fdroute_product']
								->findByRankUp($FdRoute->getRank());
		if(is_null($TargetFdRoute)) {
			false;
		}
		
		// ランクを入れ替える
		$rank = $TargetFdRoute->getRank();
		$TargetFdRoute->setRank($FdRoute->getRank());
		$FdRoute->setRank($rank);
		
		// 更新日設定
		$FdRoute->setUpdateDate($currentDateTime);
		$TargetFdRoute->setUpdateDate($currentDateTime);
		
		// 更新
		$em->persist($FdRoute);
		$em->persist($TargetFdRoute);

		$em->flush();

		return true;
	}

	/**
	 * おすすめ商品情報の順位を下げる
	 * @param $fdrouteId
	 * @return bool
	 */
	public function rankDown($fdrouteId) {
		$currentDateTime = new \DateTime();
		$em = $this->app['orm.em'];

		// おすすめ商品情報を取得する
		$FdRoute =$this->app['eccube.plugin.fdroute.repository.fdroute_product']->find($fdrouteId);
		if(is_null($FdRoute)) {
			false;
		}
		// 対象ランクの上に位置するおすすめ商品を取得する
		$TargetFdRoute =$this->app['eccube.plugin.fdroute.repository.fdroute_product']
								->findByRankDown($FdRoute->getRank());
		if(is_null($TargetFdRoute)) {
			false;
		}
		
		// ランクを入れ替える
		$rank = $TargetFdRoute->getRank();
		$TargetFdRoute->setRank($FdRoute->getRank());
		$FdRoute->setRank($rank);
		
		// 更新日設定
		$FdRoute->setUpdateDate($currentDateTime);
		$TargetFdRoute->setUpdateDate($currentDateTime);
		
		// 更新
		$em->persist($FdRoute);
		$em->persist($TargetFdRoute);

		$em->flush();

		return true;
	}

	/**
	 * おすすめ商品情報を生成する
	 * @param $data
	 * @return \Plugin\FdRoute\Entity\FdRouteProduct
	 */
	protected function newFdRoute($data) {
		$dateTime = new \DateTime();

		$rank = $this->app['eccube.plugin.fdroute.repository.fdroute_product']->getMaxRank();

		$FdRoute = new \Plugin\FdRoute\Entity\FdRouteProduct();

		$FdRoute->setConditions($data['conditions']);
		$FdRoute->setRouteString($data['route_string']);
		$FdRoute->setRouteStringPos($data['route_string_pos']);
		$FdRoute->setFdString($data['fd_string']);

		$FdRoute->setRank(($rank ? $rank : 0) + 1);
		$FdRoute->setDelFlg(Constant::DISABLED);
		$FdRoute->setCreateDate($dateTime);
		$FdRoute->setUpdateDate($dateTime);

		return $FdRoute;
	}

	private $CurrFdRoute;
	//セッションに判定したFDルートを保存する
	/*
	判定はランク順に見て、ルートは最初に一致したもの、FDは最後に一致したもの
	*/
	public function registFdRoute(){
		// dump($this->app['request']);

		if(!empty($this->app['request']->query->all())){
			$this->app['request']->getSession()->remove('route_name');
			$this->app['request']->getSession()->remove('fd_num');
			$this->app['request']->getSession()->remove('param');

			$param_arr = array();
			foreach ($this->app['request']->query->all() as $key => $val) {
				$param_arr[$key] = $val;
			}

			$this->app['request']->getSession()->set('param',$param_arr);
		}

		// FDルート管理一覧取得（rankの昇順）
		$list = $this->app['eccube.plugin.fdroute.repository.fdroute_product']->findList();

		$param_arr = !empty($this->app['request']->getSession()->get('param'))?$this->app['request']->getSession()->get('param'):array();

		$route_col = array(1=>'',2=>'',3=>'');
		$fd_num = '';
		foreach ($list as $key => $val) {
			$conditions = explode('=',$val['conditions']);
			if((array_key_exists($conditions[0],$param_arr) && array_search($conditions[1],$param_arr)) || $val['conditions'] == '*'){
				if($val['conditions'] == '*'){
					$fd_num = empty($fd_num)?$val['fd_string']:$fd_num;
				}else{
					$route_col[intval($val['route_string_pos'])] = !empty($route_col[intval($val['route_string_pos'])])?$route_col[intval($val['route_string_pos'])]:$val['route_string'].'_';
					$fd_num = $val['fd_string'];
				}
			}
		}
		$this->app['request']->getSession()->set('fd_num',$fd_num);

		$ua = $this->app['request']->server->get('HTTP_USER_AGENT');
		$browser = ((strpos($ua, 'iPhone') !== false) || (strpos($ua, 'iPod') !== false) || ((strpos($ua, 'Android') !== false) && (strpos($ua, 'Mobile') !== false)));
		$browser = ( $browser ) ? "SP" : "PC";

		$route = 'WEB_'.$browser.'_'.$route_col[1].$route_col[2].$route_col[3];
		$this->app['request']->getSession()->set('route_name',$route);
		$this->CurrFdRoute = $route;


		dump($this->app['request']->getSession()->get('route_name'));
		dump($this->app['request']->getSession()->get('fd_num'));

		return $this->CurrFdRoute;
	}
	//セッションに保存してあるFDルートを取得する
	public function getStoredFdRoute(){


		return $this->CurrFdRoute;
	}

}
