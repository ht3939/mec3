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
use Plugin\KintoneTransAdmin\Controller\kintoneAgent;

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

    public function sendKintone($req,$Order){
        dump('sendkitone');

        dump($req);
        dump($req->getRequestURI());
        dump('check req');

        $app = $this->app;
        $config = $app['config'];

        $KintoneTransAdmin =$app['eccube.plugin.kintonetransadmin.repository.kintonetransadmin_product']
                ->findOneBy(array('tagtype'=>'kintone','enable_flg'=>1,'tagurl'=> $req->getRequestURI(),'del_flg'=>0));
        if(is_null($KintoneTransAdmin)){

        }else{
        dump($KintoneTransAdmin);
        dump($Order);
            $type ="WEB完結";
            $route = "test-route";//route_setting::getRoute($type);

            $i_name = $_SESSION['customer_info']['i_name'];
            $i_kana = $_SESSION['customer_info']['i_kana'];
            $i_tel  = $_SESSION['customer_info']['i_tel'];
            $i_mail = $_SESSION['customer_info']['i_mail'];
            $i_gender = ($_SESSION['customer_info']['i_gender'] == "1") ? "男性" : "女性";
            $i_zip   = $_SESSION['customer_info']['i_zip'];
            $i_pref = $define->prefArr[$_SESSION['customer_info']['i_pref']];
            $i_add1  = $_SESSION['customer_info']['i_add1'];
            $i_add2  = $_SESSION['customer_info']['i_add2'];
            $i_card  = $_SESSION['customer_info']['i_card'];
            $i_limit_month = $_SESSION['customer_info']['i_limit_month'];
            $i_limit_year = $_SESSION['customer_info']['i_limit_year'];
            $i_holder  = $_SESSION['customer_info']['i_holder'];
            $i_credit = $define->creditArr[$_SESSION['customer_info']['i_credit']];
            $i_code  = $_SESSION['customer_info']['i_code'];
            $i_message = $_SESSION['customer_info']['i_message'];
            
            //備考追加
            //$i_message .= cart_user_note::getUserNote($cart_sp_array,$cart_sim_array);


            $kn = new kintoneAgent(
                $config['kintoneapi_url'],
                $config['kintoneapi_id'],
                $config['kintoneapi_pw'],
                $config['kintoneapi_appid']
                );
die();

            $kn->AddRecord(array(
                    "Route"             => array("value" => $route),
                    "MainProgress"      => array("value" => "未処理"),
                    "Name"              => array("value" => $i_name),
                    "Kana"              => array("value" => $i_kana),
                    "Year_Birth_Day"    => array("value" => ""),
                    "Gender"            => array("value" => $i_gender),
                    "Zip"               => array("value" => $i_zip),
                    "Add1"              => array("value" => $i_pref.$i_add1),
                    "Add2"              => array("value" => $i_add2),
                    "Add3"              => array("value" => ""),
                    "Tel"               => array("value" => $i_tel),
                    "Mail"              => array("value" => $i_mail),
                    "Card_Num"          => array("value" => $i_card),
                    "Card_Name"         => array("value" => $i_holder),
                    "Card_Type"         => array("value" => $i_credit),
                    "Card_Limit_Year"   => array("value" => $i_limit_year),
                    "Card_Limit_Month"  => array("value" => sprintf("%02d", $i_limit_month)),
                    "card_cord"         => array("value" => $i_code),
                    "Password"          => array("value" => ""),
                    "Message"           => array("value" => $i_message)

                ));



        }


    }

}


