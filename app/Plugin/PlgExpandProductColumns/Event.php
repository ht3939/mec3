<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
* http://www.lockon.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\PlgExpandProductColumns;

use Eccube\Common\Constant;
use Eccube\Event\TemplateEvent;
use Plugin\PlgExpandProductColumns\Controller\PlgExpandProductColumnsCsvImportController;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Eccube\Event\EventArgs;

class Event
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 拡張項目の保存
     *
     * 商品の新規登録時はURLにProductのidが存在しないので
     * 登録後のイベントで保存することにする
     *
     * @param FilterResponseEvent $event
     */
    public function saveExColValue(FilterResponseEvent $event)
    {
        $app = $this->app;
        if ('POST' === $app['request']->getMethod()) {

            // ProductControllerの登録成功時のみ処理を通す
            // RedirectResponseかどうかで判定する.
            $response = $event->getResponse();
            if (!$response instanceof RedirectResponse) {
                return;
            }

            // 保存したい値が入っているか確認
            if (!(isset($app['plgExpandProductColumnsValue_temp'])
                && is_array($app['plgExpandProductColumnsValue_temp']))
            ) {
                return;
            }

            /* @var $Product \Eccube\Entity\Product */
            $Product = $this->getTargetProduct($event);
            $builder = $app['form.factory']->createBuilder('admin_product');
            if ($Product->hasProductClass()) {
                $builder->remove('class');
            }

            $form = $builder->getForm();
            $form->handleRequest($app['request']);
            if ($form->get('admin_plg_expand_product_columns_value')->isValid()) {
                $save_data = $app['plgExpandProductColumnsValue_temp'];
                $repository = $app['orm.em']->getRepository('\Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumnsValue');

                foreach ($save_data as $data) {
                    /**
                     * 値が入っていなければ保存しない
                     */
                    if ($data['value']==="" && empty($data['value'])) {
                        continue;
                    }
                    $repository->save(
                        $Product->getId(),
                        $data['column_id'],
                        $data['value']
                    );
                }
                unset($app['plgExpandProductColumnsValue_temp']);
            }
        }
    }

    private function getTargetProduct($event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        if ($request->attributes->get('id')) {
            $id = $request->attributes->get('id');
        } else {
            $location = explode('/', $response->headers->get('location'));
            $url = explode('/', $this->app->url('admin_product_product_edit', array('id' => '0')));
            $diffs = array_values(array_diff($location, $url));
            $id = $diffs[0];
        }

        $Product = $this->app['eccube.repository.product']->find($id);

        return $Product;
    }

    public function onRenderAdminCsvImport(TemplateEvent $event)
    {
        /**
         * twigコードにソースを挿入
         */
        // 独自TWIGを追加する
        $snipet = 
<<<EOD
{% for header in ex_headers %}
    <td id="file_format_box__{{ header.id }}">{{ header.description|raw }}</td> 
{% endfor %}
EOD;
        $search = '<td id="file_format_box__category_delete_flg">設定されていない場合<br>0を登録</td>';
        $replace = $search.$snipet;
        $source = str_replace($search, $replace, $event->getSource());
        $event->setSource($source);

        // twigパラメータを編集する方法
        $parameters = $event->getParameters();
        $parameters['ex_headers'] = PlgExpandProductColumnsCsvImportController::getExColumnHeaders($this->app);
        $event->setParameters($parameters);
    }

    public function onRenderAdminProductNew(TemplateEvent $event)
    {
        /**
         * twigコードにソースを挿入
         */
        // 独自JSを追加する
        $snipet = html_entity_decode(file_get_contents(__DIR__. '/Resource/assets/js/product.js.twig'));
        $search = '{% endblock javascript %}';
        $replace = $snipet.$search;
        $source = str_replace($search, $replace, $event->getSource());

        // デフォルトJSを一部編集する(画像アップロードのDrop範囲を限定する)
        $snipet2 = 'dropZone: $("#drag-drop-area"),';
        $search2 = "$('#{{ form.product_image.vars.id }}').fileupload({";
        $replace2 = $search2.$snipet2;
        $source2 = str_replace($search2, $replace2, $source);

        $event->setSource($source2);

        // twigパラメータを編集する方法
        $parameters = $event->getParameters();
        $ex_images = array();
        if (!is_null($parameters['id'])) {
            $ex_images = $this->app['eccube.plugin.repository.plg_expand_product_columns_value']->getExProductImages($parameters['id']);
        }
        $parameters['ex_images'] = $ex_images;
        $event->setParameters($parameters);
    }

    public function addContentOnProductEdit(FilterResponseEvent $event)
    {
        $app = $this->app;
        $request = $event->getRequest();
        $response = $event->getResponse();
        $id = $request->attributes->get('id');

        $html = $response->getContent();
        $crawler = new Crawler($html);


        $ex_columns_value = null;
        // if ($id) {
        //     $Product = $app['eccube.repository.product']->find($id);
        //     $ex_columns_value = $this->app['eccube.plugin.repository.plg_expand_product_columns_value']
        //         ->findBy(array('productId' => $Product->getId()));
        //     // 1件空をいれとく
        //     $ex_columns_value[] = new \Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumnsValue();
        // }
        if ($id) {
            $Product = $app['eccube.repository.product']->find($id);
            $ex_columns_value = $this->app['eccube.plugin.repository.plg_expand_product_columns_value']
                ->findAllOrderByColumnName(array('productId' => $Product->getId()));
            //    ->findBy(array('productId' => $Product->getId()));
            // 1件空をいれとく
            //dump($ex_columns_value);//die();
            $ex_columns_value[] = new \Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumnsValue();
        }

        if (is_null($ex_columns_value) || empty($ex_columns_value)) {
            $PlgExpandProductColumnsValue = new \Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumnsValue();
            $ex_columns_value = array($PlgExpandProductColumnsValue);
        }



        $form = $app['form.factory']
            ->createBuilder('admin_product')
            ->getForm();

        $form->get('admin_plg_expand_product_columns_value')
            ->setData($ex_columns_value);
        $form->handleRequest($request);

        $twig = $app->renderView(
            'PlgExpandProductColumns/Resource/template/Admin/expand_column.twig',
            array(
                'form' => $form->createView(),
            )
        );

        $oldElement = $crawler
            ->filter('.accordion')
            ->last();
        if ($oldElement->count() > 0) {
            $oldHtml = $oldElement->html();
            $newHtml = $oldHtml . $twig;

            $html = $crawler->html();
            $html = str_replace($oldHtml, $newHtml, $html);

            $response->setContent($html);
            $event->setResponse($response);
        }
    }

    public function setExpandColumns(\Symfony\Component\EventDispatcher\Event $event)
    {
        $app = $this->app;
        $value_repository = $app['orm.em']->getRepository('\Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumnsValue');
        $column_repository = $app['orm.em']->getRepository('\Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumns');
        $maker_repository = $app['eccube.plugin.maker.repository.product_maker'];

        $route = $app['request']->attributes->get('_route');

        switch ($route) {
            case 'product_detail':
                $id = $app['request']->attributes->get('id');
                $__ex_product = $this->getProductExt($id, $value_repository, $column_repository);
                $__ex_product_maker = array();
                if(!is_null($maker_repository->find($id))){
                    $__ex_product_maker['name'] = $maker_repository->find($id)->getMaker()->getName();
                    $__ex_product_maker['url'] = $maker_repository->find($id)->getMakerUrl();
                }

                $app['twig']->addGlobal('__EX_PRODUCT', $__ex_product);
                $app['twig']->addGlobal('__EX_PRODUCT_MAKER', $__ex_product_maker);


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
                    $param['CarrierValue'.$key] = '%'.$val.'%';
                    $carrier_where .= ($key > 0)?' OR':'';
                    $carrier_where .= ' pepcv2.value LIKE :CarrierValue'.$key;
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
                    ->andWhere("p.Status <> 2")
                    ->andWhere("p.del_flg <> 1")
                    ->groupBy('p.id')
                    ->setParameters($param)
                    ->getQuery();


                $matchs_product = array();
                $matchs_product = $query->getResult();


                // 商品タイプ（１：端末、２：SIMカード）が同じものは削除
                $this_product = $app['eccube.repository.product']->findOneBy(array('id' => $id));
                $this_product_type = $this_product['ProductClasses'][0]['ProductType']['id'];

                foreach ($matchs_product as $key => $val) {
                    if($this_product_type == $val['ProductClasses'][0]['ProductType']['id']){
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

                $app['twig']->addGlobal('matchs_product', $matchs_product);
                $app['twig']->addGlobal('matchs_info', $matchs_info);
                $app['twig']->addGlobal('matchs_info_maker', $matchs_info_maker);

                // ------------- /対応する端末 ------------- 


                break;
            case 'product_list':
                $__ex_product_list = array();
                $__ex_product_list_maker = array();
                $pagination = $this->getPagination($app);
                foreach ($pagination as $Product) {
                    $__ex_product_list[$Product->getId()] = $this->getProductExt($Product->getId(), $value_repository, $column_repository);
                    if(!is_null($maker_repository->find($Product->getId()))){
                        $__ex_product_list_maker[$Product->getId()]['name'] = $maker_repository->find($Product->getId())->getMaker()->getName();
                        $__ex_product_list_maker[$Product->getId()]['url'] = $maker_repository->find($Product->getId())->getMakerUrl();
                    }
                }
                $app['twig']->addGlobal('__EX_PRODUCT_LIST', $__ex_product_list);
                $app['twig']->addGlobal('__EX_PRODUCT_LIST_MAKER', $__ex_product_list_maker);

                /*$category_id = $app['request']->query->get('category_id');
                if (empty($category_id)) {
                    // 全件

                } else {
                    // カテゴリ
                }*/
                break;
            case 'admin_product':
                $__ex_product_list = array();
                $pagination = $this->getPaginationForAdmin($app);
                foreach ($pagination as $Product) {
                    $__ex_product_list[$Product->getId()] = $this->getProductExt($Product->getId(), $value_repository, $column_repository);
                }

                $app['twig']->addGlobal('__EX_PRODUCT_LIST', $__ex_product_list);
                break;

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

    /**
     *
     * eccube/src/Eccube/Controller/Admin/Product/ProductController.php
     * のinitメソッドのほぼコピー
     *
     * $page_noをちゃんと入れるのが課題
     *
     * @param $app
     * @param null $page_no
     * @return array
     */
    private function getPaginationForAdmin($app, $page_no = null)
    {
        $session = $app['session'];
        $request = $app['request'];

        $searchForm = $app['form.factory']
            ->createBuilder('admin_search_product')
            ->getForm();

        $pagination = array();

        $disps = $app['eccube.repository.master.disp']->findAll();
        $pageMaxis = $app['eccube.repository.master.page_max']->findAll();
        $page_count = $app['config']['default_page_count'];
        $page_status = null;
        $active = false;

        if ('POST' === $request->getMethod()) {

            $searchForm->handleRequest($request);

            if ($searchForm->isValid()) {
                $searchData = $searchForm->getData();

                // paginator
                $qb = $app['eccube.repository.product']->getQueryBuilderBySearchDataForAdmin($searchData);
                $page_no = 1;
                $pagination = $app['paginator']()->paginate(
                    $qb,
                    $page_no,
                    $page_count,
                    array('wrap-queries' => true)
                );

                // sessionのデータ保持
                $session->set('eccube.admin.product.search', $searchData);
            }
        } else {
            if (is_null($page_no)) {
                // sessionを削除
                $session->remove('eccube.admin.product.search');
            } else {
                // pagingなどの処理
                $searchData = $session->get('eccube.admin.product.search');
                if (!is_null($searchData)) {

                    // 公開ステータス
                    $status = $request->get('status');
                    if (!empty($status)) {
                        if ($status != $app['config']['admin_product_stock_status']) {
                            $searchData['link_status'] = $app['eccube.repository.master.disp']->find($status);
                            $searchData['status'] = null;
                            $session->set('eccube.admin.product.search', $searchData);
                        } else {
                            $searchData['stock_status'] = Constant::DISABLED;
                        }
                        $page_status = $status;
                    } else {
                        $searchData['link_status'] = null;
                        $searchData['stock_status'] = null;
                    }
                    // 表示件数
                    $pcount = $request->get('page_count');

                    $page_count = empty($pcount) ? $page_count : $pcount;

                    $qb = $app['eccube.repository.product']->getQueryBuilderBySearchDataForAdmin($searchData);
                    $pagination = $app['paginator']()->paginate(
                        $qb,
                        $page_no,
                        $page_count,
                        array('wrap-queries' => true)
                    );

                    // セッションから検索条件を復元
                    if (!empty($searchData['category_id'])) {
                        $searchData['category_id'] = $app['eccube.repository.category']->find($searchData['category_id']);
                    }
                    if (empty($status)) {
                        if (count($searchData['status']) > 0) {
                            $status_ids = array();
                            foreach ($searchData['status'] as $Status) {
                                $status_ids[] = $Status->getId();
                            }
                            $searchData['status'] = $app['eccube.repository.master.disp']->findBy(array('id' => $status_ids));
                        }
                        $searchData['link_status'] = null;
                        $searchData['stock_status'] = null;
                    }
                    $searchForm->setData($searchData);
                }
            }
        }

        return $pagination;
    }

    private function getPagination($app)
    {
        $request = $app['request'];
        $BaseInfo = $app['eccube.repository.base_info']->get();

        // Doctrine SQLFilter
        if ($BaseInfo->getNostockHidden() === Constant::ENABLED) {
            $app['orm.em']->getFilters()->enable('nostock_hidden');
        }

        // handleRequestは空のqueryの場合は無視するため
        if ($request->getMethod() === 'GET') {
            $request->query->set('pageno', $request->query->get('pageno', ''));
        }

        // searchForm
        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createNamedBuilder('', 'search_product');
        $builder->setAttribute('freeze', true);
        $builder->setAttribute('freeze_display_text', false);
        if ($request->getMethod() === 'GET') {
            $builder->setMethod('GET');
        }
        /* @var $searchForm \Symfony\Component\Form\FormInterface */
        $searchForm = $builder->getForm();
        $searchForm->handleRequest($request);

        // paginator
        $searchData = $searchForm->getData();
        $qb = $app['eccube.repository.product']->getQueryBuilderBySearchData($searchData);
        

        //無理やり拡張Tagが処理しているイベントが処理されるようにとばす。
        if ($request->getMethod() === 'GET') {
            if($request->query->get('tag_id')){
                $searchData['tag_id'] = $request->query->get('tag_id');
            }
        }

        $event = new EventArgs(
            array(
                'qb' => $qb,
                'searchData' => $searchData
            ),
            $request
        );        
        $app['eccube.event.dispatcher']->dispatch('front.product.index.search', $event);

        $pagination = $app['paginator']()->paginate(
            $qb,
            !empty($searchData['pageno']) ? $searchData['pageno'] : 1,
            $searchData['disp_number']->getId(),
            array('wrap-queries' => true)
        );

        return $pagination;
    }
}
