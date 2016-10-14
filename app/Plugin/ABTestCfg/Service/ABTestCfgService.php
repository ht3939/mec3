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

namespace Plugin\ABTestCfg\Service;

use Eccube\Application;
use Eccube\Common\Constant;

class ABTestCfgService
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
    public function createABTestCfg($data) {
        // おすすめ商品詳細情報を生成する
        $ABTestCfg = $this->newABTestCfg($data);

        $em = $this->app['orm.em'];

        // おすすめ商品情報を登録する
        $em->persist($ABTestCfg);

        $em->flush();

        return true;
    }

    /**
     * おすすめ商品情報を更新する
     * @param $data
     * @return bool
     */
    public function updateABTestCfg($data) {
        $dateTime = new \DateTime();
        $em = $this->app['orm.em'];

        // おすすめ商品情報を取得する
        $ABTestCfg =$this->app['eccube.plugin.abtestcfg.repository.abtestcfg_product']->find($data['id']);
        if(is_null($ABTestCfg)) {
            false;
        }

        // おすすめ商品情報を書き換える
        $ABTestCfg->setAbtestidentity($data['abtestidentity']);
        $ABTestCfg->setEnableflg($data['enable_flg']);
        $ABTestCfg->setHeadtags($data['headtags']);
        $ABTestCfg->setTagdevice($data['tagdevice']);
        $ABTestCfg->setConditions($data['conditions']);
        $ABTestCfg->setTagurl($data['tagurl']);
        $ABTestCfg->setAbrule($data['abrule']);
        $ABTestCfg->setAburl($data['aburl']);
        $ABTestCfg->setOrganicflg($data['organic_flg']);

        $ABTestCfg->setUpdateDate($dateTime);

        // おすすめ商品情報を更新する
        $em->persist($ABTestCfg);

        $em->flush();

        return true;
    }

    /**
     * おすすめ商品情報を削除する
     * @param $abtestcfgId
     * @return bool
     */
    public function deleteABTestCfg($abtestcfgId) {
        $currentDateTime = new \DateTime();
        $em = $this->app['orm.em'];

        // おすすめ商品情報を取得する
        $ABTestCfg =$this->app['eccube.plugin.abtestcfg.repository.abtestcfg_product']->find($abtestcfgId);
        if(is_null($ABTestCfg)) {
            false;
        }
        // おすすめ商品情報を書き換える
        $ABTestCfg->setDelFlg(Constant::ENABLED);
        $ABTestCfg->setUpdateDate($currentDateTime);

        // おすすめ商品情報を登録する
        $em->persist($ABTestCfg);

        $em->flush();

        return true;
    }

    /**
     * おすすめ商品情報の順位を上げる
     * @param $abtestcfgId
     * @return bool
     */
    public function rankUp($abtestcfgId) {
        $currentDateTime = new \DateTime();
        $em = $this->app['orm.em'];

        // おすすめ商品情報を取得する
        $ABTestCfg =$this->app['eccube.plugin.abtestcfg.repository.abtestcfg_product']->find($abtestcfgId);
        if(is_null($ABTestCfg)) {
            false;
        }
        // 対象ランクの上に位置するおすすめ商品を取得する
        $TargetABTestCfg =$this->app['eccube.plugin.abtestcfg.repository.abtestcfg_product']
                                ->findByRankUp($ABTestCfg->getRank());
        if(is_null($TargetABTestCfg)) {
            false;
        }
        
        // ランクを入れ替える
        $rank = $TargetABTestCfg->getRank();
        $TargetABTestCfg->setRank($ABTestCfg->getRank());
        $ABTestCfg->setRank($rank);
        
        // 更新日設定
        $ABTestCfg->setUpdateDate($currentDateTime);
        $TargetABTestCfg->setUpdateDate($currentDateTime);
        
        // 更新
        $em->persist($ABTestCfg);
        $em->persist($TargetABTestCfg);

        $em->flush();

        return true;
    }

    /**
     * おすすめ商品情報の順位を下げる
     * @param $abtestcfgId
     * @return bool
     */
    public function rankDown($abtestcfgId) {
        $currentDateTime = new \DateTime();
        $em = $this->app['orm.em'];

        // おすすめ商品情報を取得する
        $ABTestCfg =$this->app['eccube.plugin.abtestcfg.repository.abtestcfg_product']->find($abtestcfgId);
        if(is_null($ABTestCfg)) {
            false;
        }
        // 対象ランクの上に位置するおすすめ商品を取得する
        $TargetABTestCfg =$this->app['eccube.plugin.abtestcfg.repository.abtestcfg_product']
                                ->findByRankDown($ABTestCfg->getRank());
        if(is_null($TargetABTestCfg)) {
            false;
        }
        
        // ランクを入れ替える
        $rank = $TargetABTestCfg->getRank();
        $TargetABTestCfg->setRank($ABTestCfg->getRank());
        $ABTestCfg->setRank($rank);
        
        // 更新日設定
        $ABTestCfg->setUpdateDate($currentDateTime);
        $TargetABTestCfg->setUpdateDate($currentDateTime);
        
        // 更新
        $em->persist($ABTestCfg);
        $em->persist($TargetABTestCfg);

        $em->flush();

        return true;
    }

    /**
     * おすすめ商品情報を生成する
     * @param $data
     * @return \Plugin\ABTestCfg\Entity\ABTestCfgProduct
     */
    protected function newABTestCfg($data) {
        $dateTime = new \DateTime();

        $rank = $this->app['eccube.plugin.abtestcfg.repository.abtestcfg_product']->getMaxRank();

        $ABTestCfg = new \Plugin\ABTestCfg\Entity\ABTestCfgProduct();

        $ABTestCfg->setAbtestidentity($data['abtestidentity']);
        $ABTestCfg->setEnableflg($data['enable_flg']);
        $ABTestCfg->setHeadtags($data['headtags']);
        $ABTestCfg->setTagdevice($data['tagdevice']);
        $ABTestCfg->setConditions($data['conditions']);
        $ABTestCfg->setTagurl($data['tagurl']);
        $ABTestCfg->setAbrule($data['abrule']);
        $ABTestCfg->setAburl($data['aburl']);
        $ABTestCfg->setOrganicflg($data['organic_flg']);

        $ABTestCfg->setRank(($rank ? $rank : 0) + 1);
        $ABTestCfg->setDelFlg(Constant::DISABLED);
        $ABTestCfg->setCreateDate($dateTime);
        $ABTestCfg->setUpdateDate($dateTime);

        return $ABTestCfg;
    }

    private $CurrABTestCfg;
    //セッションに判定したFDルートを保存する
    public function registABTestCfg(){
        $rank = $this->app['eccube.plugin.abtestcfg.repository.abtestcfg_product']->findList();


        return $this->CurrABTestCfg;
    }
    //セッションに保存してあるFDルートを取得する
    public function getStoredABTestCfg(){


        return $this->CurrABTestCfg;
    }

}
