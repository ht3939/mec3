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

namespace Plugin\RecommendSimRanking\Service;

use Eccube\Application;
use Eccube\Common\Constant;

class RecommendSimRankingService
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
    public function createRecommendSimRanking($data) {
        // おすすめ商品詳細情報を生成する
        $RecommendSimRanking = $this->newRecommendSimRanking($data);

        $em = $this->app['orm.em'];

        // おすすめ商品情報を登録する
        $em->persist($RecommendSimRanking);

        $em->flush();

        return true;
    }

    /**
     * おすすめ商品情報を更新する
     * @param $data
     * @return bool
     */
    public function updateRecommendSimRanking($data) {
        $dateTime = new \DateTime();
        $em = $this->app['orm.em'];

        // おすすめ商品情報を取得する
        $RecommendSimRanking =$this->app['eccube.plugin.recommend.repository.recommend_product']->find($data['id']);
        if(is_null($RecommendSimRanking)) {
            false;
        }

        // おすすめ商品情報を書き換える
        $RecommendSimRanking->setComment($data['comment']);
        $RecommendSimRanking->setProduct($data['Product']);
        $RecommendSimRanking->setUpdateDate($dateTime);

        // おすすめ商品情報を更新する
        $em->persist($RecommendSimRanking);

        $em->flush();

        return true;
    }

    /**
     * おすすめ商品情報を削除する
     * @param $recommendId
     * @return bool
     */
    public function deleteRecommendSimRanking($recommendId) {
        $currentDateTime = new \DateTime();
        $em = $this->app['orm.em'];

        // おすすめ商品情報を取得する
        $RecommendSimRanking =$this->app['eccube.plugin.recommend.repository.recommend_product']->find($recommendId);
        if(is_null($RecommendSimRanking)) {
            false;
        }
        // おすすめ商品情報を書き換える
        $RecommendSimRanking->setDelFlg(Constant::ENABLED);
        $RecommendSimRanking->setUpdateDate($currentDateTime);

        // おすすめ商品情報を登録する
        $em->persist($RecommendSimRanking);

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
        $RecommendSimRanking =$this->app['eccube.plugin.recommend.repository.recommend_product']->find($recommendId);
        if(is_null($RecommendSimRanking)) {
            false;
        }
        // 対象ランクの上に位置するおすすめ商品を取得する
        $TargetRecommendSimRanking =$this->app['eccube.plugin.recommend.repository.recommend_product']
                                ->findByRankUp($RecommendSimRanking->getRank());
        if(is_null($TargetRecommendSimRanking)) {
            false;
        }
        
        // ランクを入れ替える
        $rank = $TargetRecommendSimRanking->getRank();
        $TargetRecommendSimRanking->setRank($RecommendSimRanking->getRank());
        $RecommendSimRanking->setRank($rank);
        
        // 更新日設定
        $RecommendSimRanking->setUpdateDate($currentDateTime);
        $TargetRecommendSimRanking->setUpdateDate($currentDateTime);
        
        // 更新
        $em->persist($RecommendSimRanking);
        $em->persist($TargetRecommendSimRanking);

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
        $RecommendSimRanking =$this->app['eccube.plugin.recommend.repository.recommend_product']->find($recommendId);
        if(is_null($RecommendSimRanking)) {
            false;
        }
        // 対象ランクの上に位置するおすすめ商品を取得する
        $TargetRecommendSimRanking =$this->app['eccube.plugin.recommend.repository.recommend_product']
                                ->findByRankDown($RecommendSimRanking->getRank());
        if(is_null($TargetRecommendSimRanking)) {
            false;
        }
        
        // ランクを入れ替える
        $rank = $TargetRecommendSimRanking->getRank();
        $TargetRecommendSimRanking->setRank($RecommendSimRanking->getRank());
        $RecommendSimRanking->setRank($rank);
        
        // 更新日設定
        $RecommendSimRanking->setUpdateDate($currentDateTime);
        $TargetRecommendSimRanking->setUpdateDate($currentDateTime);
        
        // 更新
        $em->persist($RecommendSimRanking);
        $em->persist($TargetRecommendSimRanking);

        $em->flush();

        return true;
    }

    /**
     * おすすめ商品情報を生成する
     * @param $data
     * @return \Plugin\RecommendSimRanking\Entity\RecommendSimRankingProduct
     */
    protected function newRecommendSimRanking($data) {
        $dateTime = new \DateTime();

        $rank = $this->app['eccube.plugin.recommend.repository.recommend_product']->getMaxRank();

        $RecommendSimRanking = new \Plugin\RecommendSimRanking\Entity\RecommendSimRankingProduct();
        $RecommendSimRanking->setComment($data['comment']);
        $RecommendSimRanking->setProduct($data['Product']);
        $RecommendSimRanking->setRank(($rank ? $rank : 0) + 1);
        $RecommendSimRanking->setDelFlg(Constant::DISABLED);
        $RecommendSimRanking->setCreateDate($dateTime);
        $RecommendSimRanking->setUpdateDate($dateTime);

        return $RecommendSimRanking;
    }

}
