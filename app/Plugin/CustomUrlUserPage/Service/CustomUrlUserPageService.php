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

		return true;
	}

	/**
	 * おすすめ商品情報を更新する
	 * @param $data
	 * @return bool
	 */
	public function updateCustomUrlUserPage($data) {
		$dateTime = new \DateTime();
		$em = $this->app['orm.em'];

		// おすすめ商品情報を取得する
		$CustomUrlUserPage =$this->app['eccube.plugin.recommend.repository.recommend_product']->find($data['id']);
		if(is_null($CustomUrlUserPage)) {
			false;
		}

		// おすすめ商品情報を書き換える
		$CustomUrlUserPage->setComment($data['comment']);
		$CustomUrlUserPage->setProduct($data['Product']);
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
		$CustomUrlUserPage =$this->app['eccube.plugin.recommend.repository.recommend_product']->find($recommendId);
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
		$CustomUrlUserPage =$this->app['eccube.plugin.recommend.repository.recommend_product']->find($recommendId);
		if(is_null($CustomUrlUserPage)) {
			false;
		}
		// 対象ランクの上に位置するおすすめ商品を取得する
		$TargetCustomUrlUserPage =$this->app['eccube.plugin.recommend.repository.recommend_product']
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
		$CustomUrlUserPage =$this->app['eccube.plugin.recommend.repository.recommend_product']->find($recommendId);
		if(is_null($CustomUrlUserPage)) {
			false;
		}
		// 対象ランクの上に位置するおすすめ商品を取得する
		$TargetCustomUrlUserPage =$this->app['eccube.plugin.recommend.repository.recommend_product']
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

		$rank = $this->app['eccube.plugin.recommend.repository.recommend_product']->getMaxRank();

		$CustomUrlUserPage = new \Plugin\CustomUrlUserPage\Entity\CustomUrlUserPageProduct();
		$CustomUrlUserPage->setComment($data['comment']);
		$CustomUrlUserPage->setProduct($data['Product']);
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

    // テンプレートに商品の情報を渡す
	public function getProductParam($product_list){
		$app = $this->app;
		$value_repository = $app['orm.em']->getRepository('\Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumnsValue');
		$column_repository = $app['orm.em']->getRepository('\Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumns');
		$maker_repository = $app['eccube.plugin.maker.repository.product_maker'];

		$__ex_product_list = array();
		$__ex_product_list_maker = array();
		foreach ($product_list as $Product) {
			$__ex_product_list[$Product['Product']->getId()] = $this->getProductExt($Product['Product']->getId(), $value_repository, $column_repository);
			if(!is_null($maker_repository->find($Product['Product']->getId()))){
				$__ex_product_list_maker[$Product['Product']->getId()]['name'] = $maker_repository->find($Product['Product']->getId())->getMaker()->getName();
				$__ex_product_list_maker[$Product['Product']->getId()]['url'] = $maker_repository->find($Product['Product']->getId())->getMakerUrl();
			}
		}

		return array(
			'__EX_PRODUCT_LIST' => $__ex_product_list,
			'__EX_PRODUCT_LIST_MAKER' => $__ex_product_list_maker,
			);
	}


	// Plugin\PlgExpandProductColumns\Event.php からコピペ
	private function getProductExt($id, $value_repository, $column_repository)
	{
		$product_ex = array();
		$columns = $column_repository->findAll();
//dump("test");

		/** @var \Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumns $column */
		foreach ($columns as $column) {
			$value = $value_repository->findOneBy(array(
				'columnId' => $column->getColumnId(),
				'productId' => $id));
			/**
			 * 配列系の値の場合、配列にしてから渡す
			 */
			switch ($column->getColumnType()) {
				case EX_TYPE_IMAGE :
				case EX_TYPE_CHECKBOX :
					if (empty($value)) {
						$value = '';
					} else {
						$value = explode(',', $value->getValue());
					}
					break;
				default :
					$value = empty($value) ? '' : $value->getValue();
			}
			$valuetext = '';
			$valset = explode("\r\n",$column->getColumnSetting());
			//dump($valset);
			$vss = array();
			foreach($valset as $vs){
				if(!empty($vs)){

					$vs =  explode(':',$vs);
					if(isset($vs[0])){
					$vss[$vs[0]] = $vs[1];
					}
				}
			}
			//dump($vss);
			

			switch ($column->getColumnType()) {
				case EX_TYPE_CHECKBOX :
					if (empty($value)) {
						$valuetext = '';
					} else {
						foreach($value as $v){
							$valuetext[] = $vss[$v];
						}
					}
					break;

				case EX_TYPE_SELECT :
				case EX_TYPE_RADIO :
					if (empty($value)) {
						$valuetext = '';
					} else {
						$valuetext = $vss[$value];
					}
					break;
				default :
					$valuetext = $value;
			}

			$product_st[$column->getColumnName()] = array(
				'id' => $column->getColumnId(),
				'name' => $column->getColumnName(),
				'value' => $value
				,'valuetext'=> $valuetext
			);

			$product_ex[$column->getColumnId()] = array(
				'id' => $column->getColumnId(),
				'name' => $column->getColumnName(),
				'value' => $value
				,'valuetext'=> $valuetext
			);
		}
		ksort($product_st);
		$product_ex=array();
		foreach($product_st as $ex){
			$product_ex[$ex['id']] = $ex;
		}

//dump($product_st);
		return $product_ex;
	}


}
