<?php

namespace Plugin\MatchProduct\Controller\Block;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Plugin\MatchProduct\Entity\MatchProduct;


class MatchProductController
{
    //タイトル
    // private $_title = 'この商品と同じメーカーの商品';

    // // 表示個数
    // private $_show_count = 4; //初期値4

    // // 関連商品用データ
    // private $_rp = null;

    // // 価格の表示/非表示
    // private $_show_price = null;

    /**
     * MatchProduct画面
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        $id = $app['request']->attributes->get('id');


        if($id){
            $value_repository = $app['orm.em']->getRepository('\Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumnsValue');
            $column_repository = $app['orm.em']->getRepository('\Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumns');
            $maker_repository = $app['eccube.plugin.maker.repository.product_maker'];
            $__ex_product = $this->getProductExt($id, $value_repository, $column_repository);

            $product = $app['eccube.repository.product']->find($id);

            // ------------- 対応する端末 ------------- 

            $param = array();

            // ************ 対応するカードサイズ ************ 
            $param['SimColumn'] = $app['config']['product_ex_sim_size'];
            $this_sim_size = ($__ex_product[$param['SimColumn']]['value'])?$__ex_product[$param['SimColumn']]['value']:array();
            $sim_where = 'pepcv1.columnId = :SimColumn AND ';
            if(array_search('value4',$this_sim_size) === false){
                foreach ($this_sim_size as $key => $val) {
                    $param['SimValue'.$key] = '%'.$val.'%';
                    $sim_where .= ($key > 0)?' OR':'';
                    $sim_where .= ' pepcv1.value LIKE :SimValue'.$key;
                }
                // カードサイズが設定されていない場合、絶対一致しない条件を入れる
                if(empty($this_sim_size)){
                    $sim_where .= '1 = 2';
                }else{
                    $sim_where .= ' OR pepcv1.value LIKE \'value4\'';
                }
            }else{
                // マルチSIMが選択されていた場合、全商品対象に（value4 = マルチSIM）
                $sim_where = ':SimColumn = :SimColumn';
            }

            $sub_query_sim = $app['orm.em']->getRepository('\Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumnsValue')->createQueryBuilder('pepcv1')
                ->select('pepcv1.productId')
                ->where($sim_where)
                // ->setParameters($param)
                ->groupBy('pepcv1.productId');

            // ************ /対応するカードサイズ ************ 

            // // ************ 対応するキャリア ************ 
            $param['CarrierColumn'] = $app['config']['product_ex_carrier'];
            $this_carrier = ($__ex_product[$param['CarrierColumn']]['value'])?$__ex_product[$param['CarrierColumn']]['value']:array();
            $carrier_where = 'pepcv2.columnId = :CarrierColumn AND ';
            foreach ($this_carrier as $key => $val) {
                $carrier_where .= ($key == 0)?' ( ':'';
                $param['CarrierValue'.$key] = '%'.$val.'%';
                $carrier_where .= ($key > 0)?' OR':'';
                $carrier_where .= ' pepcv2.value LIKE :CarrierValue'.$key;
            }
            if(count($this_carrier)>1){
                $carrier_where .= ' )';

            }
            // キャリアが設定されていない場合、絶対一致しない条件を入れる
            if(empty($this_carrier))$carrier_where .= '1 = 2';

            $sub_query_carrier = $app['orm.em']->getRepository('\Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumnsValue')->createQueryBuilder('pepcv2')
                ->select('pepcv2.productId')
                ->where($carrier_where)
                // ->setParameters($param)
                ->groupBy('pepcv2.productId');

            //log_debug($sub_query_carrier->getQuery());
            //log_debug($sub_query_carrier->getQuery()->getResult());
            // ************ /対応するキャリア ************ 


            $query = $app['eccube.repository.product']->createQueryBuilder('p')
                ->where("p.id IN ({$sub_query_sim->getDql()})")
                ->andWhere("p.id IN ({$sub_query_carrier->getDql()})")
                ->andWhere("p.Status = 1")
                ->andWhere("p.del_flg = 0")
                ->groupBy('p.id')
                ->setParameters($param)
                ->getQuery();

            $matchs_product = array();
            $matchs_product = $query->getResult();

            // 商品タイプ（１：端末、２：SIMカード）が同じものは削除
            $this_product = $app['eccube.repository.product']->findOneBy(array('id' => $id));
            $this_product_type = $this_product['ProductClasses'][0]['ProductType']['id'];


            $taggrp = array_merge($app['config']['matchproduct_ex_target_device_type_grp']
                ,$app['config']['matchproduct_ex_target_sim_type_grp']);
            if(in_array($this_product_type,$app['config']['matchproduct_ex_target_sim_type_grp']) ){
                $deltag = $app['config']['matchproduct_ex_target_sim_type_grp'];

            }
            if(in_array($this_product_type,$app['config']['matchproduct_ex_target_device_type_grp']) ){
                $deltag = $app['config']['matchproduct_ex_target_device_type_grp'];

            }
            foreach ($matchs_product as $key => $val) {
                //
                if(
                    in_array($val['ProductClasses'][0]['ProductType']['id'],$deltag)
                    ){
                    unset($matchs_product[$key]);
                }
                //対象外
                if(
                    !in_array($val['ProductClasses'][0]['ProductType']['id'],$taggrp)
                    ){
                    unset($matchs_product[$key]);
                }
            }
            $matchs_product = array_merge($matchs_product);

            $matchs_info = array();
            $matchs_info_maker = array();
            foreach ($matchs_product as $key => $val) {
                $match_id = $val['id'];
                $matchs_info[$match_id] = $this->getProductExt($match_id, $value_repository, $column_repository);
                if(!is_null($maker_repository->find($match_id))){
                    $matchs_info_maker[$match_id]['name'] = $maker_repository->find($match_id)->getMaker()->getName();
                    $matchs_info_maker[$match_id]['url'] = $maker_repository->find($match_id)->getMakerUrl();
                }
            }
            // ------------- /対応する端末 ------------- 

            return $app['view']->render("Block/match_product.twig", array(
                'Product' => $product,
                'ProductEx' => $__ex_product,
                'this_product_type' => $this_product_type,
                'matchs_product' => $matchs_product,
                'matchs_info' => $matchs_info,
                'matchs_info_maker' => $matchs_info_maker
            ));


        }else{
            return $app['view']->render("Block/match_product.twig", array(
                'this_product_type' => null,
                'matchs_product' => array(),
                'matchs_info' => null,
                'matchs_info_maker' => null
            ));
        }



    }



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
