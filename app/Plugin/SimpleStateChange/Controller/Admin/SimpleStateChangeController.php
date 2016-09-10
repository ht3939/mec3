<?php
/*
 * Copyright(c) 2016 SYSTEM_KD
 */


namespace Plugin\SimpleStateChange\Controller\Admin;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Eccube\Common\Constant;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;

class SimpleStateChangeController
{
    public function index(Application $app, Request $request)
    {
        $arrResult = array();
        $arrResult['ret'] = 0;

        $id = $request->get('id');

        /* @var $Product \Eccube\Entity\Product */
        $Product = array();

        if(empty($id)) {
            $Product = array();
        } else {
            $Product = $app['eccube.repository.product']->find($id);
        }

        if(!empty($Product)) {

            /* @var $DispON \Eccube\Entity\Master\Disp */
            /* @var $DispOFF \Eccube\Entity\Master\Disp */

            $DispON = $app['eccube.repository.master.disp']->find(\Eccube\Entity\Master\Disp::DISPLAY_SHOW);
            $DispOFF = $app['eccube.repository.master.disp']->find(\Eccube\Entity\Master\Disp::DISPLAY_HIDE);

            if($Product->isEnable()) {
                $Product->setStatus($DispOFF);
                $arrResult['now_status'] = $DispOFF->getId();
                $arrResult['status_value'] = $DispON->getName() . "する";
            } else {
                $Product->setStatus($DispON);
                $arrResult['now_status'] = $DispON->getId();
                $arrResult['status_value'] = $DispOFF->getName() . "にする";
            }


            $app['orm.em']->persist($Product);
            $app['orm.em']->flush();

            $arrResult['ret'] = 1;
        }

        $response = new Response(json_encode($arrResult));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function statusUpdate(Application $app, Request $request)
    {

        $this->app = $app;

        $builder = $app['form.factory']->createBuilder('admin_update_status');

        $form = $builder->getForm();

        if($request->isXmlHttpRequest()) {

            if ('POST' === $request->getMethod()) {

                // タイムアウトを無効にする.
                set_time_limit(0);
                ini_set('memory_limit', '512M');

                $form->handleRequest($request);

                if (!$form->isValid()) {
                    // エラー
                    $arrResult = array();
                    $arrResult['ret'] = 0;

                    $response = new Response(json_encode($arrResult));
                    $response->headers->set('Content-Type', 'application/json');

                    return $response;
                }

                // 対象の商品を取得
                $session = $app['session'];
                $searchData = $session->get('eccube.admin.product.search');

                // ページ条件制御
                $status = $request->get('status');
                if ($status == $app['config']['admin_product_stock_status']) {
                    $searchData['stock_status'] = Constant::DISABLED;
                }

                $app['eccube.repository.master.disp']->findAll();

                $qb = $app['eccube.repository.product']->getQueryBuilderBySearchDataForAdmin($searchData);

                // プラグインでの検索条件拡張を考慮してフックポイントを設置
                $event = new EventArgs(
                    array(
                        'qb' => $qb,
                        'searchData' => $searchData,
                    ),
                    $request
                    );
                $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_PRODUCT_INDEX_SEARCH, $event);
                $searchData = $event->getArgument('searchData');

                $query = $qb->getQuery();

                // 結果
                $arrResult = array();

                $update_count = 0;

                // ステータス変更
                /* @var $Disp \Eccube\Entity\Master\Disp */
                $Disp = $form->get('Status')->getData();

//                 print_r($Status);exit;

                $this->em = $app['orm.em'];
                // SQL実行
                $this->em->getConfiguration()->setSQLLogger(null);

                $update_count = 0;

                /* @var $Product \Eccube\Entity\Product */
                foreach ($query->getResult() as $Product) {

                    $Product->setStatus($Disp);

                    $this->em->persist($Product);
                    $query->free();

                    $update_count++;
                }
                $this->em->flush();

                $arrResult['ret'] = 1;
                $arrResult['update_count'] = $update_count;

                $response = new Response(json_encode($arrResult));
                $response->headers->set('Content-Type', 'application/json');

                return $response;

            } else {


                $title = '種別一括変更';
                $sub_title = '';
                $detail = "※選択した種別へ一括で更新を行います。";
                $mode = "update";
                $button = "一括変更";

                $Disp = $app['eccube.repository.master.disp']->find(\Eccube\Entity\Master\Disp::DISPLAY_SHOW);
                $form->setData(array('Status'=> $Disp));

                // タグ情報返却
                $view = 'SimpleStateChange/Resource/template/admin/Product/status_view.twig';
                return $app->render($view, array(
                    'title' => $title,
                    'sub_title' => $sub_title,
                    'detail' => $detail,
                    'form' => $form->createView(),
                    'mode' => $mode,
                    'button' => $button,
                ));

            }

        }

        return false;

    }
}