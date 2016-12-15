<?php
/*
 * Copyright(c) 2015 SystemFriend Inc. All rights reserved.
 * http://ec-cube.systemfriend.co.jp/
 */

namespace Plugin\ExcludeProductPayment\HookPoint\Front;

use Plugin\ExcludeProductPayment\HookPoint\HookBaseService;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * 商品ページ画面のフック
 */
class ProductDetailService extends HookBaseService
{
    /**
     * 商品ページ
     *
     */
    public function onControllerBefore()
    {
        $app           = $this->app;
        $const         = $this->const;

        // 初期設定
        /* @var $Util \Plugin\ExcludeProductPayment\Service\UtilService */
        /* @var $Setting \Plugin\ExcludeProductPayment\Service\ConfigService */
        $product_id = $app['request']->attributes->get('id');
        $Util    = $app['eccube.plugin.service.cpr.util'];
        $Setting = $app['eccube.plugin.service.cpr.config'];

        // 管理画面からの確認かどうかのチェック
        /* @var $Product \Eccube\Entity\Product */
        $Product = $app['eccube.repository.product']->find($product_id);
        if (!empty($Product)){
            if ($app['request']->getSession()->has('_security_admin') && $Product->getStatus()->getId() !== 1) {
                return;
            }
        }

        $product_non = false;
        if (empty($product_id)){
            $product_non = true;
        }

        $Product = $app['eccube.repository.product']->find($product_id);
        if (empty($Product)){
            $product_non = true;
        }

        // 非公開、商品なし、商品削除の場合　非公開リダイレクト実行
        $redirect_run = $product_non;

        // 非公開、商品なし、商品削除の場合　非公開リダイレクト実行
        if (!$product_non){
            $redirect_run = ($Product->getStatus()->getId() == 2);
        }

        // 非公開リダイレクト 管理画面からではない場合
        if ($redirect_run){
            $redirect_url        = '';
            $redirect_product_id = '';
            $redirect_action     = '';

            // 商品設定のリダイレクト
            if (!$product_non){
                /* @var $CprProductRedirectRepo \Plugin\ExcludeProductPayment\Repository\CprProductRedirectRepository */
                $CprProductRedirectRepo = $app['eccube.plugin.repository.cpr.product_redirect'];

                /* @var $CprProductRedirect \Plugin\ExcludeProductPayment\Entity\CprProductRedirect */
                $CprProductRedirect = $CprProductRedirectRepo->find($product_id);
                if (!empty($CprProductRedirect)){
                    // 商品へのリダイレクトの場合
                    $this->checkRedirectProduct($CprProductRedirect, $redirect_url, $redirect_product_id, $redirect_action);

                    // URLへのリダイレクトの場合
                    $this->checkRedirectUrl($CprProductRedirect, $redirect_url, $redirect_product_id, $redirect_action);
                }
            }

            // URLの設定がない場合、設定値から取得
            if (empty($redirect_url)){
                list($redirect_url, $redirect_action) = $Setting->getConfigUrl();
            }

            $isOpenGuide = false;
            // アプリケーション外へのリダイレクトかどうかをチェック
            if ($redirect_action == $const['redirect_url']){
                // アプリケーション外への直接のリダイレクトは扱わない
                $isOpenGuide = $Util->checkInOutUrl($redirect_url);
            }

            // リダイレクト案内ページへの遷移
            $arrPlugin = $Setting->getSetting();
            // 案内ページを表示 かつ IDかURLかで遷移 かつ URLが設定済み かつ 外へのリンクの場合
            $isRedirectGuide = $arrPlugin['guide_flg'] == 1 && $redirect_action > 0 && !empty($redirect_url) || $isOpenGuide;
            if ($isRedirectGuide){
                $this->redirectSiteUrl('cpr_redirect_guide', array('redirectAction'=>$redirect_action, 'url'=>$redirect_url));
            }

            // リダイレクト
            if (!empty($redirect_url)){
                $this->redirectUrl($redirect_url);
            }
        }
    }

    /**
     * リダイレクト先商品の場合の処理
     *
     * @param \Plugin\ExcludeProductPayment\Entity\CprProductRedirect $CprProductRedirect
     * @param string $redirect_url
     * @param integer $redirect_product_id
     * @param integer $redirect_action
     */
    private function checkRedirectProduct($CprProductRedirect, &$redirect_url, &$redirect_product_id, &$redirect_action){
        $const = $this->const;

        /* @var $Util \Plugin\ExcludeProductPayment\Service\UtilService */
        $Util = $this->app['eccube.plugin.service.cpr.util'];

        $isID = $CprProductRedirect->getRedirectSelect()  == $const['redirect_id'];
        if ($isID){
            $redirect_product_id = $Util->getRedirectProductId($CprProductRedirect->getId());

            if (!empty($redirect_product_id)){
                $redirect_url    = $this->app->url('product_detail', array('id'=>$redirect_product_id));
                $redirect_action = $const['redirect_id'];
            }
        }
    }

    /**
     * リダイレクト先URLの場合の処理
     *
     * @param \Plugin\ExcludeProductPayment\Entity\CprProductRedirect $CprProductRedirect
     * @param string $redirect_url
     * @param integer $redirect_product_id
     * @param integer $redirect_action
     */
    private function checkRedirectUrl($CprProductRedirect, &$redirect_url, &$redirect_product_id, &$redirect_action){
        $const = $this->const;

        // URLへのリダイレクト
        $isUrl = $CprProductRedirect->getRedirectSelect() == $const['redirect_url'];
        if ($isUrl){
            $redirect_url    = $CprProductRedirect->getRedirectUrl();
            $redirect_action = $const['redirect_url'];
        }else if($redirect_product_id > 0){
            // 商品IDでのリダイレクト先が非公開の場合で、リダイレクト設定が、URL、未設定の場合の処理
            /* @var $ProductRepo \Eccube\Repository\ProductRepository */
            $ProductRepo = $this->app['eccube.repository.product'];
            $qb = $ProductRepo->createQueryBuilder('p');
            $qb->select('p.id, pr.redirect_select, pr.redirect_url')
                ->leftJoin('Plugin\ExcludeProductPayment\Entity\CprProductRedirect', 'pr', 'WITH', 'p.id = pr.id')
                ->andWhere('p.id      = :product_id')
                ->andWhere('p.Status  = 2')
                ->andWhere('p.del_flg = 0')
                ->setParameter('product_id' , $redirect_product_id)
                ->setMaxResults(1);

            try {
                $arrRProduct = $qb->getQuery()->getSingleResult();
            } catch (\Doctrine\Orm\NoResultException $e) {
                $arrRProduct = array();
            }

            $exist_flg = true;
            if (array_key_exists('redirect_select', $arrRProduct)){
                $temp_action = empty($arrRProduct['redirect_select']) ? $const['redirect_non']:$arrRProduct['redirect_select'];
            }else{
                $temp_action = $const['redirect_non']; // 値に意味なし
                $exist_flg = false;
            }

            if ($temp_action == $const['redirect_url']){
                $redirect_url    = $arrRProduct['redirect_url'];
                $redirect_action = $const['redirect_url'];
            }else if ($temp_action == $const['redirect_non'] && $exist_flg){
                $redirect_url    = '';
                $redirect_action = $const['redirect_non'];
            }
        }
    }

}
