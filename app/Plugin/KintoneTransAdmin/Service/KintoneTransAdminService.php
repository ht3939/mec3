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

namespace Plugin\KintoneTransAdmin\Service;

use Eccube\Application;
use Eccube\Common\Constant;

class KintoneTransAdminService
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
    public function createKintoneTransAdmin($data) {
        // おすすめ商品詳細情報を生成する
        $KintoneTransAdmin = $this->newKintoneTransAdmin($data);

        $em = $this->app['orm.em'];

        // おすすめ商品情報を登録する
        $em->persist($KintoneTransAdmin);

        $em->flush();

        return true;
    }

    /**
     * おすすめ商品情報を更新する
     * @param $data
     * @return bool
     */
    public function updateKintoneTransAdmin($data) {
        $dateTime = new \DateTime();
        $em = $this->app['orm.em'];

        // おすすめ商品情報を取得する
        $KintoneTransAdmin =$this->app['eccube.plugin.kintonetransadmin.repository.kintonetransadmin_product']->find($data['id']);
        if(is_null($KintoneTransAdmin)) {
            false;
        }

        // おすすめ商品情報を書き換える
        $KintoneTransAdmin->setTagtype($data['tagtype']);
        $KintoneTransAdmin->setEnableflg($data['enable_flg']);
        $KintoneTransAdmin->setConditions($data['conditions']);
        $KintoneTransAdmin->setTagurl($data['tagurl']);

        $KintoneTransAdmin->setUpdateDate($dateTime);

        // おすすめ商品情報を更新する
        $em->persist($KintoneTransAdmin);

        $em->flush();

        return true;
    }

    /**
     * おすすめ商品情報を削除する
     * @param $kintonetransadminId
     * @return bool
     */
    public function deleteKintoneTransAdmin($kintonetransadminId) {
        $currentDateTime = new \DateTime();
        $em = $this->app['orm.em'];

        // おすすめ商品情報を取得する
        $KintoneTransAdmin =$this->app['eccube.plugin.kintonetransadmin.repository.kintonetransadmin_product']->find($kintonetransadminId);
        if(is_null($KintoneTransAdmin)) {
            false;
        }
        // おすすめ商品情報を書き換える
        $KintoneTransAdmin->setDelFlg(Constant::ENABLED);
        $KintoneTransAdmin->setUpdateDate($currentDateTime);

        // おすすめ商品情報を登録する
        $em->persist($KintoneTransAdmin);

        $em->flush();

        return true;
    }

    /**
     * おすすめ商品情報を生成する
     * @param $data
     * @return \Plugin\KintoneTransAdmin\Entity\KintoneTransAdminProduct
     */
    protected function newKintoneTransAdmin($data) {
        $dateTime = new \DateTime();

        //$rank = $this->app['eccube.plugin.kintonetransadmin.repository.kintonetransadmin_product']->getMaxRank();

        $KintoneTransAdmin = new \Plugin\KintoneTransAdmin\Entity\KintoneTransAdminProduct();

        $KintoneTransAdmin->setTagtype($data['tagtype']);
        $KintoneTransAdmin->setEnableflg($data['enable_flg']);
        $KintoneTransAdmin->setConditions($data['conditions']);
        $KintoneTransAdmin->setTagurl($data['tagurl']);

        $KintoneTransAdmin->setDelFlg(Constant::DISABLED);
        $KintoneTransAdmin->setCreateDate($dateTime);
        $KintoneTransAdmin->setUpdateDate($dateTime);

        return $KintoneTransAdmin;
    }

    private $CurrKintoneTransAdmin;
    //セッションに判定したFDルートを保存する
    public function registKintoneTransAdmin(){
        $rank = $this->app['eccube.plugin.kintonetransadmin.repository.kintonetransadmin_product']->findList();


        return $this->CurrKintoneTransAdmin;
    }
    //セッションに保存してあるFDルートを取得する
    public function getStoredKintoneTransAdmin(){


        return $this->CurrKintoneTransAdmin;
    }

}
