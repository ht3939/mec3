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

namespace Plugin\RecommendSumahoRanking
\Service;

use Eccube\Application;
use Eccube\Common\Constant;

class RecommendSumahoRanking
Service
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
    public function createRecommendSumahoRanking
($data) {
        // おすすめ商品詳細情報を生成する
        $RecommendSumahoRanking
 = $this->newRecommendSumahoRanking
($data);

        $em = $this->app['orm.em'];

        // おすすめ商品情報を登録する
        $em->persist($RecommendSumahoRanking
);

        $em->flush();

        return true;
    }

    /**
     * おすすめ商品情報を更新する
     * @param $data
     * @return bool
     */
    public function updateRecommendSumahoRanking
($data) {
        $dateTime = new \DateTime();
        $em = $this->app['orm.em'];

        // おすすめ商品情報を取得する
        $RecommendSumahoRanking
 =$this->app['eccube.plugin.recommendsumahoranking.repository.recommendsumahoranking_product']->find($data['id']);
        if(is_null($RecommendSumahoRanking
)) {
            false;
        }

        // おすすめ商品情報を書き換える
        $RecommendSumahoRanking
->setComment($data['comment']);
        $RecommendSumahoRanking
->setProduct($data['Product']);
        $RecommendSumahoRanking
->setUpdateDate($dateTime);

        // おすすめ商品情報を更新する
        $em->persist($RecommendSumahoRanking
);

        $em->flush();

        return true;
    }

    /**
     * おすすめ商品情報を削除する
     * @param $recommendsumahorankingId
     * @return bool
     */
    public function deleteRecommendSumahoRanking
($recommendsumahorankingId) {
        $currentDateTime = new \DateTime();
        $em = $this->app['orm.em'];

        // おすすめ商品情報を取得する
        $RecommendSumahoRanking
 =$this->app['eccube.plugin.recommendsumahoranking.repository.recommendsumahoranking_product']->find($recommendsumahorankingId);
        if(is_null($RecommendSumahoRanking
)) {
            false;
        }
        // おすすめ商品情報を書き換える
        $RecommendSumahoRanking
->setDelFlg(Constant::ENABLED);
        $RecommendSumahoRanking
->setUpdateDate($currentDateTime);

        // おすすめ商品情報を登録する
        $em->persist($RecommendSumahoRanking
);

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
        $RecommendSumahoRanking
 =$this->app['eccube.plugin.recommendsumahoranking.repository.recommendsumahoranking_product']->find($recommendsumahorankingId);
        if(is_null($RecommendSumahoRanking
)) {
            false;
        }
        // 対象ランクの上に位置するおすすめ商品を取得する
        $TargetRecommendSumahoRanking
 =$this->app['eccube.plugin.recommendsumahoranking.repository.recommendsumahoranking_product']
                                ->findByRankUp($RecommendSumahoRanking
->getRank());
        if(is_null($TargetRecommendSumahoRanking
)) {
            false;
        }
        
        // ランクを入れ替える
        $rank = $TargetRecommendSumahoRanking
->getRank();
        $TargetRecommendSumahoRanking
->setRank($RecommendSumahoRanking
->getRank());
        $RecommendSumahoRanking
->setRank($rank);
        
        // 更新日設定
        $RecommendSumahoRanking
->setUpdateDate($currentDateTime);
        $TargetRecommendSumahoRanking
->setUpdateDate($currentDateTime);
        
        // 更新
        $em->persist($RecommendSumahoRanking
);
        $em->persist($TargetRecommendSumahoRanking
);

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
        $RecommendSumahoRanking
 =$this->app['eccube.plugin.recommendsumahoranking.repository.recommendsumahoranking_product']->find($recommendsumahorankingId);
        if(is_null($RecommendSumahoRanking
)) {
            false;
        }
        // 対象ランクの上に位置するおすすめ商品を取得する
        $TargetRecommendSumahoRanking
 =$this->app['eccube.plugin.recommendsumahoranking.repository.recommendsumahoranking_product']
                                ->findByRankDown($RecommendSumahoRanking
->getRank());
        if(is_null($TargetRecommendSumahoRanking
)) {
            false;
        }
        
        // ランクを入れ替える
        $rank = $TargetRecommendSumahoRanking
->getRank();
        $TargetRecommendSumahoRanking
->setRank($RecommendSumahoRanking
->getRank());
        $RecommendSumahoRanking
->setRank($rank);
        
        // 更新日設定
        $RecommendSumahoRanking
->setUpdateDate($currentDateTime);
        $TargetRecommendSumahoRanking
->setUpdateDate($currentDateTime);
        
        // 更新
        $em->persist($RecommendSumahoRanking
);
        $em->persist($TargetRecommendSumahoRanking
);

        $em->flush();

        return true;
    }

    /**
     * おすすめ商品情報を生成する
     * @param $data
     * @return \Plugin\RecommendSumahoRanking
\Entity\RecommendSumahoRanking
Product
     */
    protected function newRecommendSumahoRanking
($data) {
        $dateTime = new \DateTime();

        $rank = $this->app['eccube.plugin.recommendsumahoranking.repository.recommendsumahoranking_product']->getMaxRank();

        $RecommendSumahoRanking
 = new \Plugin\RecommendSumahoRanking
\Entity\RecommendSumahoRanking
Product();
        $RecommendSumahoRanking
->setComment($data['comment']);
        $RecommendSumahoRanking
->setProduct($data['Product']);
        $RecommendSumahoRanking
->setRank(($rank ? $rank : 0) + 1);
        $RecommendSumahoRanking
->setDelFlg(Constant::DISABLED);
        $RecommendSumahoRanking
->setCreateDate($dateTime);
        $RecommendSumahoRanking
->setUpdateDate($dateTime);

        return $RecommendSumahoRanking
;
    }

}
