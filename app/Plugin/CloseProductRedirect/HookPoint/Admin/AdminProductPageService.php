<?php
/*
 * Copyright(c) 2015 SystemFriend Inc. All rights reserved.
 * http://ec-cube.systemfriend.co.jp/
 */

namespace Plugin\CloseProductRedirect\HookPoint\Admin;

use Plugin\CloseProductRedirect\HookPoint\HookBaseService;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DomCrawler\Crawler;
use Eccube\Util\Cache;

/**
 * 商品登録ページ画面のフック
 */
class AdminProductPageService extends HookBaseService
{
    /**
     * 商品登録ページ
     *
     * @param FilterResponseEvent $event
     */
    public function onRenderBefore(FilterResponseEvent $event)
    {
        $app           = $this->app;
        $request       = $event->getRequest();
        $response      = $event->getResponse();

        // 初期設定
        $product_id = $app['request']->attributes->get('id');

        /* @var $CprProductRedirectRepo \Plugin\CloseProductRedirect\Repository\CprProductRedirectRepository */
        $CprProductRedirectRepo = $app['eccube.plugin.repository.cpr.product_redirect'];

        /* @var $CprProductRedirect \Plugin\CloseProductRedirect\Entity\CprProductRedirect */
        if ($product_id > 0){
            $CprProductRedirect = $CprProductRedirectRepo->find($product_id);
        }else{
            $CprProductRedirect = new \Plugin\CloseProductRedirect\Entity\CprProductRedirect;
        }

        if ($product_id > 0){
            /* @var $Product \Eccube\Entity\Product */
            $Product = $app['eccube.repository.product']->find($product_id);
        }

        if (empty($Product)){
            $has_class = false;
        }else{
            $has_class = $Product->hasProductClass();
        }

        // フォームタイプの生成
        $builder = $app['form.factory']->createBuilder('admin_product');
        if ($has_class){
            $builder->remove('class');
        }

        // データの設定
        $form = $builder->getForm();
        $form->get('ProductRedirect')->setData($CprProductRedirect);

        $form->handleRequest($request);

        $error_flg = 0;
        // POST時の処理
        if ('POST' === $request->getMethod()) {
            if ($form->isValid()) {
                // 登録は商品登録画面が登録できるときに行う
                // RedirectResponseかどうかで判定する.
                if ($response instanceof RedirectResponse) {
                    $product_id = $this->getTarget($event, 'admin_product_product_edit');
                    $em = $app['orm.em'];
                    $em->getConnection()->beginTransaction();
                    try {
                        $CprProductRedirect = $form->get('ProductRedirect')->getData();
                        $CprProductRedirect->setId($product_id);

                        $em->persist($CprProductRedirect);
                        $em->flush();
                        $em->getConnection()->commit();   // コミット
                        Cache::clear($app, false);
                    } catch (\Exception $e) {
                        $em->getConnection()->rollback(); // ロールバック
                    }
                }
            }else{
                $error_flg = 1;
            }
        }

        $twig = $app->renderView(
            'CloseProductRedirect/Resource/template/admin/Product/product_page.twig',
            array(
                'form' => $form->createView(),
                'error_flg' => $error_flg,
            )
        );

        $response = $event->getResponse();

        $html = $response->getContent();
        $crawler = new Crawler($html);

        $oldElement = $crawler->filter('#aside_wrap .box.form-horizontal')->first();

        $this->insertAfter($crawler, $oldElement,  $twig, $response);
        $event->setResponse($response);
    }
}
