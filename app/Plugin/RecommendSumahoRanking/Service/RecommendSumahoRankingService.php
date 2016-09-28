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

namespace Plugin\recommendsumahorankingSumahoRanking\Service;

use Eccube\Application;
use Eccube\Common\Constant;

class recommendsumahorankingSumahoRankingService
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
    public function createrecommendsumahorankingSumahoRanking($data) {
        // おすすめ商品詳細情報を生成する
        $recommendsumahorankingSumahoRanking = $this->newrecommendsumahorankingSumahoRanking($data);

        $em = $this->app['orm.em'];

        // おすすめ商品情報を登録する
        $em->persist($recommendsumahorankingSumahoRanking);

        $em->flush();

        return true;
    }

    /**
     * おすすめ商品情報を更新する
     * @param $data
     * @return bool
     */
    public function updaterecommendsumahorankingSumahoRanking($data) {
        $dateTime = new \DateTime();
        $em = $this->app['orm.em'];

        // おすすめ商品情報を取得する
        $recommendsumahorankingSumahoRanking =$this->app['eccube.plugin.recommendsumahoranking.repository.recommendsumahoranking_product']->find($data['id']);
        if(is_null($recommendsumahorankingSumahoRanking)) {
            false;
        }

        // おすすめ商品情報を書き換える
        $recommendsumahorankingSumahoRanking->setComment($data['comment']);
        $recommendsumahorankingSumahoRanking->setProduct($data['Product']);
        $recommendsumahorankingSumahoRanking->setUpdateDate($dateTime);

        // おすすめ商品情報を更新する
        $em->persist($recommendsumahorankingSumahoRanking);

        $em->flush();

        return true;
    }

    /**
     * おすすめ商品情報を削除する
     * @param $recommendsumahorankingId
     * @return bool
     */
    public function deleterecommendsumahorankingSumahoRanking($recommendsumahorankingId) {
        $currentDateTime = new \DateTime();
        $em = $this->app['orm.em'];

        // おすすめ商品情報を取得する
        $recommendsumahorankingSumahoRanking =$this->app['eccube.plugin.recommendsumahoranking.repository.recommendsumahoranking_product']->find($recommendsumahorankingId);
        if(is_null($recommendsumahorankingSumahoRanking)) {
            false;
        }
        // おすすめ商品情報を書き換える
        $recommendsumahorankingSumahoRanking->setDelFlg(Constant::ENABLED);
        $recommendsumahorankingSumahoRanking->setUpdateDate($currentDateTime);

        // おすすめ商品情報を登録する
        $em->persist($recommendsumahorankingSumahoRanking);

        $em->flush();

        return true;
    }

    /**
     * おすすめ商品情報の順位を上げる
     * @param $recommendsumahorankingId
     * @return bool
     */
    public function rankUp($recommendsumahorankingId) {
        $currentDateTime = new \DateTime();
        $em = $this->app['orm.em'];

        // おすすめ商品情報を取得する
        $recommendsumahorankingSumahoRanking =$this->app['eccube.plugin.recommendsumahoranking.repository.recommendsumahoranking_product']->find($recommendsumahorankingId);
        if(is_null($recommendsumahorankingSumahoRanking)) {
            false;
        }
        // 対象ランクの上に位置するおすすめ商品を取得する
        $TargetrecommendsumahorankingSumahoRanking =$this->app['eccube.plugin.recommendsumahoranking.repository.recommendsumahoranking_product']
                                ->findByRankUp($recommendsumahorankingSumahoRanking->getRank());
        if(is_null($TargetrecommendsumahorankingSumahoRanking)) {
            false;
        }
        
        // ランクを入れ替える
        $rank = $TargetrecommendsumahorankingSumahoRanking->getRank();
        $TargetrecommendsumahorankingSumahoRanking->setRank($recommendsumahorankingSumahoRanking->getRank());
        $recommendsumahorankingSumahoRanking->setRank($rank);
        
        // 更新日設定
        $recommendsumahorankingSumahoRanking->setUpdateDate($currentDateTime);
        $TargetrecommendsumahorankingSumahoRanking->setUpdateDate($currentDateTime);
        
        // 更新
        $em->persist($recommendsumahorankingSumahoRanking);
        $em->persist($TargetrecommendsumahorankingSumahoRanking);

        $em->flush();

        return true;
    }

    /**
     * おすすめ商品情報の順位を下げる
     * @param $recommendsumahorankingId
     * @return bool
     */
    public function rankDown($recommendsumahorankingId) {
        $currentDateTime = new \DateTime();
        $em = $this->app['orm.em'];

        // おすすめ商品情報を取得する
        $recommendsumahorankingSumahoRanking =$this->app['eccube.plugin.recommendsumahoranking.repository.recommendsumahoranking_product']->find($recommendsumahorankingId);
        if(is_null($recommendsumahorankingSumahoRanking)) {
            false;
        }
        // 対象ランクの上に位置するおすすめ商品を取得する
        $TargetrecommendsumahorankingSumahoRanking =$this->app['eccube.plugin.recommendsumahoranking.repository.recommendsumahoranking_product']
                                ->findByRankDown($recommendsumahorankingSumahoRanking->getRank());
        if(is_null($TargetrecommendsumahorankingSumahoRanking)) {
            false;
        }
        
        // ランクを入れ替える
        $rank = $TargetrecommendsumahorankingSumahoRanking->getRank();
        $TargetrecommendsumahorankingSumahoRanking->setRank($recommendsumahorankingSumahoRanking->getRank());
        $recommendsumahorankingSumahoRanking->setRank($rank);
        
        // 更新日設定
        $recommendsumahorankingSumahoRanking->setUpdateDate($currentDateTime);
        $TargetrecommendsumahorankingSumahoRanking->setUpdateDate($currentDateTime);
        
        // 更新
        $em->persist($recommendsumahorankingSumahoRanking);
        $em->persist($TargetrecommendsumahorankingSumahoRanking);

        $em->flush();

        return true;
    }

    /**
     * おすすめ商品情報を生成する
     * @param $data
     * @return \Plugin\recommendsumahorankingSumahoRanking\Entity\recommendsumahorankingSumahoRankingProduct
     */
    protected function newrecommendsumahorankingSumahoRanking($data) {
        $dateTime = new \DateTime();

        $rank = $this->app['eccube.plugin.recommendsumahoranking.repository.recommendsumahoranking_product']->getMaxRank();

        $recommendsumahorankingSumahoRanking = new \Plugin\recommendsumahorankingSumahoRanking\Entity\recommendsumahorankingSumahoRankingProduct();
        $recommendsumahorankingSumahoRanking->setComment($data['comment']);
        $recommendsumahorankingSumahoRanking->setProduct($data['Product']);
        $recommendsumahorankingSumahoRanking->setRank(($rank ? $rank : 0) + 1);
        $recommendsumahorankingSumahoRanking->setDelFlg(Constant::DISABLED);
        $recommendsumahorankingSumahoRanking->setCreateDate($dateTime);
        $recommendsumahorankingSumahoRanking->setUpdateDate($dateTime);

        return $recommendsumahorankingSumahoRanking;
    }

}
