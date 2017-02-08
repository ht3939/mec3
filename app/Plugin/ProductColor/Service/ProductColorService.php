<?php

namespace Plugin\ProductColor\Service;

use Eccube\Application;
use Eccube\Common\Constant;

class ProductColorService
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
    public function createProductColorsByCsv(\Eccube\Entity\Product $Product,$csvdata) {
        $em = $this->app['orm.em'];
        $ProductColorRepo = $this->app['eccube.plugin.productcolor.repository.product_productcolor']
                        ->findBy(array('Product'=>$Product));
        if($ProductColorRepo){
            foreach($ProductColorRepo as $row){
                $em->remove($row);
            }
            $em->flush();
        }


        $csvdataarr = explode(",",$csvdata);
        if(count($csvdataarr)>0){
            foreach($csvdataarr as $cr){
                if(!empty($cr)){
                    $ctag = $this->app['eccube.plugin.productcolor.repository.productcolor']->findBy(array('name'=>$cr));
                    if($ctag){
                        $ctag = $ctag[0];


                    }else{
                        $ctag = new \Plugin\ProductColor\Entity\ProductColor();
                        $ctag->setName($cr);
                        $ctag->setCreateDate(new \Datetime());
                        $ctag->setUpdateDate(new \Datetime());
                        $ctag->setDelFlg(0);

                        $ctagRankMax = $this->app['eccube.plugin.productcolor.repository.productcolor']
                                            ->createQueryBuilder('m')
                                                    ->orderBy('m.rank', 'DESC')
                                                    ->setMaxResults(1)
                                                    ->getQuery()
                                                    ->getResult();
                        if($ctagRankMax){
                            $ctag->setRank($ctagRankMax[0]->getRank()+1);

                        }else{
                            $ctag->setRank(1);

                        }

                        $em->persist($ctag);
                        $em->flush();

     
                    }

                    $cctag = new \Plugin\ProductColor\Entity\ProductProductColor();
                    $cctag->setProduct($Product);
                    $cctag->setProductColor($ctag);
                    $cctag->setCreateDate(new \Datetime());

                    $em->persist($cctag);

                    $em->flush();

                }

            }
        }

        return true;
    }

    /**
     * @param $data
     * @return bool
     */
    public function getProductColorAll() {
        $em = $this->app['orm.em'];
        $ProductColorRepo = $this->app['eccube.plugin.productcolor.repository.product_productcolor']
                                    ->createQueryBuilder('m')
                                    ->orderBy('m.Product', 'ASC')
                                    ->getQuery()
                                    ->getResult();
        if($ProductColorRepo){
            $datas = array();
            $dataTags = array();

            foreach($ProductColorRepo as $ProductColor){
                $id = $ProductColor->getProductColor()->getId();
                $ctag = $this->app['eccube.plugin.productcolor.repository.productcolor']
                                ->find($id);
                if($ctag){
                    $dataTags[$ProductColor->getProduct()->getId()] .= $ctag->getName().",";
                }

            }
            foreach($dataTags as $key=>$row){
                $datas[] = array('id'=>$key,
                                    'refid'=> $row
                                    );
            }

        }



        return $datas;
    }

}


