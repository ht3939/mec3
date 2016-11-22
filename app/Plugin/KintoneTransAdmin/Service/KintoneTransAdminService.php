<?php

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
     * @param $data
     * @return bool
     */
    public function createKintoneTransAdmin($data) {
        $KintoneTransAdmin = $this->newKintoneTransAdmin($data);

        $em = $this->app['orm.em'];
        $em->persist($KintoneTransAdmin);
        $em->flush();

        return true;
    }

    /**
     * @param $data
     * @return bool
     */
    public function updateKintoneTransAdmin($data) {
        $dateTime = new \DateTime();
        $em = $this->app['orm.em'];

        $KintoneTransAdmin =$this->app['eccube.plugin.kintonetransadmin.repository.kintonetransadmin_product']->find($data['id']);
        if(is_null($KintoneTransAdmin)) {
            false;
        }

        $KintoneTransAdmin->setTagtype($data['tagtype']);
        $KintoneTransAdmin->setEnableflg($data['enable_flg']);
        $KintoneTransAdmin->setConditions($data['conditions']);
        $KintoneTransAdmin->setTagurl($data['tagurl']);

        $KintoneTransAdmin->setUpdateDate($dateTime);

        $em->persist($KintoneTransAdmin);

        $em->flush();

        return true;
    }

    /**
     * @param $kintonetransadminId
     * @return bool
     */
    public function deleteKintoneTransAdmin($kintonetransadminId) {
        $currentDateTime = new \DateTime();
        $em = $this->app['orm.em'];

        $KintoneTransAdmin =$this->app['eccube.plugin.kintonetransadmin.repository.kintonetransadmin_product']->find($kintonetransadminId);
        if(is_null($KintoneTransAdmin)) {
            false;
        }
        $KintoneTransAdmin->setDelFlg(Constant::ENABLED);
        $KintoneTransAdmin->setUpdateDate($currentDateTime);

        $em->persist($KintoneTransAdmin);

        $em->flush();

        return true;
    }

    /**
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

    public function sendKintone($req,$data){
        //dump('sendkitone');
        // $Order = $data['Order'];
        // $route = $data['Route'];
        // $note  = $data['Note'];
        // dump($data);

        // dump($req);
        // dump($req->getRequestURI());
        // dump('check req');

        $app = $this->app;
        $config = $app['config'];
        // dump($req->getRequestURI());
        $KintoneTransAdmin =$app['eccube.plugin.kintonetransadmin.repository.kintonetransadmin_product']
                ->findOneBy(array('tagtype'=>'kintone',
                    'enable_flg'=>1,
                    'tagurl'=> $req->getRequestURI(),
                    'del_flg'=>0)
                )
        ;
        if(is_null($KintoneTransAdmin)){
            //転送対象なし
        }else{
            // dump($KintoneTransAdmin);
            // dump($Order);
            // dump($route);
            // dump($note);
            $paramjson = $KintoneTransAdmin->getConditions();
            // dump($KintoneTransAdmin->getConditions());
            // dump(json_decode($paramjson,true));
            $conditionarr = json_decode($paramjson,true);
            if(!is_array($conditionarr)){
                //システムエラー
                throw new \InvalidArgumentException(sprintf('KintoneTrans "%s" admin setting pattern is invalid json format.','-'));
            }
            //変換処理
            $addrec = array();
            foreach($conditionarr as $key=>$value){
                if(!empty($value)){
                    $val = explode(".",$value);
                    if(count($val)>1){
                        if(isset($data[$val[0]][$val[1]])){
                            $addrec[$key] = array("value"=>$data[$val[0]][$val[1]]);

                        }else{
                            //未指定なので、スキップ
                            //throw new \InvalidArgumentException(sprintf('KintoneTrans Argument "%s" not found.', $val[0].$val[1]));

                        }

                    }else{
                        //システムエラー
                        throw new \InvalidArgumentException(sprintf('KintoneTrans Argument "%s" parameter pattern is invalid.', $val[0]));
                    }
                }

            }
            // dump('addrec');
            // dump($addrec);



            $kn = new kintoneAgent(
                $config['kintoneapi_url'],
                $config['kintoneapi_id'],
                $config['kintoneapi_pw'],
                $config['kintoneapi_appid']
                );

                //throw new \Exception(sprintf('KintoneTrans Argument "%s" sending kintone API was fail.', '-'));

            $knres = $kn->AddRecord($addrec);
            if($knres == false){
                //システムエラー
                throw new \Exception(sprintf('KintoneTrans Argument "%s" sending kintone API was fail.', '-'));
            }

            // dump('kintone res');
            // dump($knres);//die();


        }


    }

    public function sendAwsSQS($req,$data){
        //not impliments.

    }

}


