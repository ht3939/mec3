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

namespace Plugin\CustomUrlUserPage\Service;

use Eccube\Application;
use Eccube\Common\Constant;

class CustomUrlUserPageService
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
	public function createCustomUrlUserPage($data) {
		// おすすめ商品詳細情報を生成する
		$CustomUrlUserPage = $this->newCustomUrlUserPage($data);

		$em = $this->app['orm.em'];

		// おすすめ商品情報を登録する
		$em->persist($CustomUrlUserPage);

		$em->flush();

		return $CustomUrlUserPage;
	}

	/**
	 * おすすめ商品情報を更新する
	 * @param $data
	 * @return bool
	 */
	public function updateCustomUrlUserPage($data) {
		$dateTime = new \DateTime();
		$em = $this->app['orm.em'];
dump($data);//die();
		// おすすめ商品情報を取得する
		$CustomUrlUserPage =$this->app['eccube.plugin.customurluserpage.repository.customurluserpage']->find($data['id']);
		if(is_null($CustomUrlUserPage)) {
			return false;
		}
		$CustomUrlUserPage->setCustomUrl($data['customurl']);
		$CustomUrlUserPage->setUserPage($data['userpage']);
		$CustomUrlUserPage->setBindName($data['bindname']);
		$CustomUrlUserPage->setPageThumbnail($data['pagethumbnail']);
		$CustomUrlUserPage->setPageInfo($data['pageinfo']);
		$CustomUrlUserPage->setPageCategoryKey($data['pagecategorykey']);
		$CustomUrlUserPage->setIndexFlg($data['index_flg']);
		$CustomUrlUserPage->setPageLayout($data['pagelayout']);

		$CustomUrlUserPage->setUpdateDate($dateTime);

		// おすすめ商品情報を更新する
		$em->persist($CustomUrlUserPage);

		$em->flush();

		return true;
	}

	/**
	 * おすすめ商品情報を削除する
	 * @param $recommendId
	 * @return bool
	 */
	public function deleteCustomUrlUserPage($recommendId) {
		$currentDateTime = new \DateTime();
		$em = $this->app['orm.em'];

		// おすすめ商品情報を取得する
		$CustomUrlUserPage =$this->app['eccube.plugin.customurluserpage.repository.customurluserpage']->find($recommendId);
		if(is_null($CustomUrlUserPage)) {
			false;
		}
		// おすすめ商品情報を書き換える
		$CustomUrlUserPage->setDelFlg(Constant::ENABLED);
		$CustomUrlUserPage->setUpdateDate($currentDateTime);

		// おすすめ商品情報を登録する
		$em->persist($CustomUrlUserPage);

		$em->flush();

		return true;
	}

	/**
	 * おすすめ商品情報の順位を上げる
	 * @param $recommendId
	 * @return bool
	 */
	public function rankUp($recommendId) {
		$currentDateTime = new \DateTime();
		$em = $this->app['orm.em'];

		// おすすめ商品情報を取得する
		$CustomUrlUserPage =$this->app['eccube.plugin.customurluserpage.repository.customurluserpage']->find($recommendId);
		if(is_null($CustomUrlUserPage)) {
			false;
		}
		// 対象ランクの上に位置するおすすめ商品を取得する
		$TargetCustomUrlUserPage =$this->app['eccube.plugin.customurluserpage.repository.customurluserpage']
								->findByRankUp($CustomUrlUserPage->getRank());
		if(is_null($TargetCustomUrlUserPage)) {
			false;
		}
		
		// ランクを入れ替える
		$rank = $TargetCustomUrlUserPage->getRank();
		$TargetCustomUrlUserPage->setRank($CustomUrlUserPage->getRank());
		$CustomUrlUserPage->setRank($rank);
		
		// 更新日設定
		$CustomUrlUserPage->setUpdateDate($currentDateTime);
		$TargetCustomUrlUserPage->setUpdateDate($currentDateTime);
		
		// 更新
		$em->persist($CustomUrlUserPage);
		$em->persist($TargetCustomUrlUserPage);

		$em->flush();

		return true;
	}

	/**
	 * おすすめ商品情報の順位を下げる
	 * @param $recommendId
	 * @return bool
	 */
	public function rankDown($recommendId) {
		$currentDateTime = new \DateTime();
		$em = $this->app['orm.em'];

		// おすすめ商品情報を取得する
		$CustomUrlUserPage =$this->app['eccube.plugin.customurluserpage.repository.customurluserpage']->find($recommendId);
		if(is_null($CustomUrlUserPage)) {
			false;
		}
		// 対象ランクの上に位置するおすすめ商品を取得する
		$TargetCustomUrlUserPage =$this->app['eccube.plugin.customurluserpage.repository.customurluserpage']
								->findByRankDown($CustomUrlUserPage->getRank());
		if(is_null($TargetCustomUrlUserPage)) {
			false;
		}
		
		// ランクを入れ替える
		$rank = $TargetCustomUrlUserPage->getRank();
		$TargetCustomUrlUserPage->setRank($CustomUrlUserPage->getRank());
		$CustomUrlUserPage->setRank($rank);
		
		// 更新日設定
		$CustomUrlUserPage->setUpdateDate($currentDateTime);
		$TargetCustomUrlUserPage->setUpdateDate($currentDateTime);
		
		// 更新
		$em->persist($CustomUrlUserPage);
		$em->persist($TargetCustomUrlUserPage);

		$em->flush();

		return true;
	}

	/**
	 * おすすめ商品情報を生成する
	 * @param $data
	 * @return \Plugin\CustomUrlUserPage\Entity\CustomUrlUserPageProduct
	 */
	protected function newCustomUrlUserPage($data) {
		$dateTime = new \DateTime();

		$rank = $this->app['eccube.plugin.customurluserpage.repository.customurluserpage']->getMaxRank();

		$CustomUrlUserPage = new \Plugin\CustomUrlUserPage\Entity\CustomUrlUserPage();
		$CustomUrlUserPage->setCustomUrl($data['customurl']);
		$CustomUrlUserPage->setUserPage($data['userpage']);
		$CustomUrlUserPage->setBindName($data['bindname']);
		$CustomUrlUserPage->setPageThumbnail($data['pagethumbnail']);
		$CustomUrlUserPage->setPageInfo($data['pageinfo']);
		$CustomUrlUserPage->setPageCategoryKey($data['pagecategorykey']);
		$CustomUrlUserPage->setIndexFlg($data['index_flg']);

		$CustomUrlUserPage->setPageLayout($data['pagelayout']);

		$CustomUrlUserPage->setRank(($rank ? $rank : 0) + 1);
		$CustomUrlUserPage->setDelFlg(Constant::DISABLED);
		$CustomUrlUserPage->setCreateDate($dateTime);
		$CustomUrlUserPage->setUpdateDate($dateTime);

		return $CustomUrlUserPage;
	}

    public function checkInstallPlugin($code)
    {
        $Plugin = $this->app['eccube.repository.plugin']->findOneBy(array('code' => $code, 'enable' => 1));

        if($Plugin){
            return true;
        }else{
            return false;
        }
    }





}
