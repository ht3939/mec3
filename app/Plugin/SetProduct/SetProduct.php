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

namespace Plugin\SetProduct;

use Eccube\Common\Constant;
use Eccube\Event\EventArgs;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class SetProduct
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function onFrontShoppingIndexInitialize(EventArgs $event){
        $service = $this->app['eccube.plugin.setproduct.service.setproduct'];
        $service->SetShopingProductSetProduct($event);

    }

    public function onRenderAdminProductNewBefore(FilterResponseEvent $event)
    {
        $app = $this->app;
        if (!$this->app->isGranted('ROLE_ADMIN')) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();
        $id = $request->attributes->get('id');

        list($html, $form) = $this->getHtml($request, $response, $id);
        $response->setContent($html);

        if ('POST' === $request->getMethod()) {
            // RedirectResponseかどうかで判定する.

            if (!$response instanceof RedirectResponse) {
                return;
            }
            if (empty($id)) {
                $location = explode('/', $response->headers->get('location'));
                $url = explode('/', $this->app->url('admin_product_product_edit', array('id' => '0')));
                $diffs = array_values(array_diff($location, $url));
                $id = $diffs[0];
            }

            if ($form->get('setproduct_maker')->isValid()) {
                // 登録
                $data = $form->getData();

                $Makers = $this->app['eccube.plugin.maker.repository.maker']->findAll();

                $Maker = $form->get('setproduct_maker')->getData();
                $setproductSimFlg = $form->get('setproduct_sim_flg')->getData();

                if (count($Makers) > 0 && !empty($Maker)) {

                    $ProductSetProduct = new \Plugin\SetProduct\Entity\ProductSetProduct();

                    $ProductSetProduct
                        ->setId($id)
                        ->setSetProductSimFlg($setproductSimFlg)
                        ->setDelFlg(Constant::DISABLED)
                        ->setMaker($Maker);

                    $app['orm.em']->persist($ProductSetProduct);

                    $app['orm.em']->flush($ProductSetProduct);
                }
            }
        }

        $event->setResponse($response);
    }

    public function onRenderAdminProductEditBefore(FilterResponseEvent $event)
    {
        $app = $this->app;
        if (!$app->isGranted('ROLE_ADMIN')) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();
        $id = $request->attributes->get('id');

        list($html, $form) = $this->getHtml($request, $response, $id);
        $response->setContent($html);

        $event->setResponse($response);
    }


    public function onAdminProductEditAfter()
    {
        $app = $this->app;
        if (!$app->isGranted('ROLE_ADMIN')) {
            return;
        }

        $id = $app['request']->attributes->get('id');

        $form = $app['form.factory']
            ->createBuilder('admin_product')
            ->getForm();

        $ProductSetProduct = $app['eccube.plugin.setproduct.repository.product_setproduct']->find($id);

        if (is_null($ProductSetProduct)) {
            $ProductSetProduct = new \Plugin\SetProduct\Entity\ProductSetProduct();
        }

        $form->get('setproduct_maker')->setData($ProductSetProduct->getMaker());

        $form->handleRequest($app['request']);

        if ('POST' === $app['request']->getMethod()) {

            if ($form->get('setproduct_maker')->isValid()) {

                $maker_id = $form->get('setproduct_maker')->getData();
                if ($maker_id) {
                // 登録・更新
                    // $SetProduct = $app['eccube.plugin.setproduct.repository.setproduct']->find($setproduct_id);
                    $Maker = $app['eccube.plugin.maker.repository.maker']->find($maker_id);
                // ※setIdはなんだか違う気がする
                    if ($id) {
                        $ProductSetProduct->setId($id);
                    }

                    $ProductSetProduct
                        ->setSetProductSimFlg($form->get('setproduct_sim_flg')->getData())
                        ->setDelFlg(0)
                        ->setMaker($Maker);
                        $app['orm.em']->persist($ProductSetProduct);
                } else {
                // 削除
                // ※setIdはなんだか違う気がする
                    $ProductSetProduct->setId($id);
                    $app['orm.em']->remove($ProductSetProduct);
                }

                $app['orm.em']->flush();
            }
        }
    }

    private function getHtml($request, $response, $id)
    {
        // dump($this->app);
        // exit;

        // メーカーマスタから有効なメーカー情報を取得
        $Makers = $this->app['eccube.plugin.maker.repository.maker']->findAll();
        // $SetProducts = $this->app['eccube.plugin.setproduct.repository.product_setproduct']->findAll();

        if (is_null($Makers)) {
            $Makers = new \Plugin\Maker\Entity\Maker();
            // $SetProducts = new \Plugin\SetProduct\Entity\SetProduct();
        }

        $ProductSetProduct = null;

        if ($id) {
            // 商品メーカーマスタから設定されているなメーカー情報を取得
            $ProductSetProduct = $this->app['eccube.plugin.setproduct.repository.product_setproduct']->find($id);
        }

        // 商品登録・編集画面のHTMLを取得し、DOM化
        $crawler = new Crawler($response->getContent());

        $form = $this->app['form.factory']
            ->createBuilder('admin_product')
            ->getForm();

        if ($ProductSetProduct) {
            // 既に登録されている商品メーカー情報が設定されている場合、初期選択
            $form->get('setproduct_maker')->setData($ProductSetProduct->getMaker());
            $form->get('setproduct_sim_flg')->setData($ProductSetProduct->getSetProductSimFlg());
        }

        $form->handleRequest($request);

        $parts = $this->app->renderView(
            'SetProduct/View/admin/product_setproduct.twig',
            array('form' => $form->createView())
        );

        // form1の最終項目に追加(レイアウトに依存
        $html = $this->getHtmlFromCrawler($crawler);

        try {
            $oldHtml = $crawler->filter('#form1 .accordion')->last()->html();//dump($oldHtml);
        $oldHtml2 = html_entity_decode($oldHtml, ENT_NOQUOTES, 'UTF-8');//dump($oldHtml2);
            $newHtml = $oldHtml2.$parts;//dump($newHtml);
            $html = str_replace($oldHtml2, $newHtml, $html);//dump($html);
        } catch (\InvalidArgumentException $e) {
            // no-op
        }

        return array($html, $form);

    }


    public function onRenderProductsDetailBefore(FilterResponseEvent $event)
    {
        $app = $this->app;
        $request = $event->getRequest();
        $response = $event->getResponse();
        $id = $request->attributes->get('id');

        $ProductSetProduct = null;

        if ($id) {
            // 商品メーカーマスタから設定されているなメーカー情報を取得
            $ProductSetProduct = $this->app['eccube.plugin.setproduct.repository.product_setproduct']->find($id);
        }
        if (!$ProductSetProduct) {
            return;
        }

        $Maker = $ProductSetProduct->getMaker();

        if (is_null($SetMaker)) {
            // 商品メーカーマスタにデータが存在しないまたは削除されていれば無視する
            return;
        }

        // HTMLを取得し、DOM化
        $crawler = new Crawler($response->getContent());
        $html = $this->getHtmlFromCrawler($crawler);

        if ($ProductSetProduct) {
            $parts = $this->app->renderView(
                'SetProduct/View/default/setproduct.twig',
                array(
                    'setproduct_name' => $ProductSetProduct->getMaker()->getName(),
                    'setproduct_sim_flg' => $ProductSetProduct->getSetProductSimFlg(),
                )
            );

            try {
                // ※商品コードの下に追加
                $parts_item_code = $crawler->filter('.item_code')->html();
                $new_html = $parts_item_code.$parts;
                $html = str_replace($parts_item_code, $new_html, $html);
            } catch (\InvalidArgumentException $e) {
                // no-op
            }
        }

        $response->setContent($html);
        $event->setResponse($response);
    }

    /**
     * 解析用HTMLを取得.
     *
     * @param Crawler $crawler
     *
     * @return string
     */
    private function getHtmlFromCrawler(Crawler $crawler)
    {
        $html = '';
        foreach ($crawler as $domElement) {
            $domElement->ownerDocument->formatOutput = true;
            $html .= $domElement->ownerDocument->saveHTML();
        }

        return html_entity_decode($html, ENT_NOQUOTES, 'UTF-8');
    }
}
