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

namespace Plugin\ProductColor;

use Eccube\Common\Constant;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Eccube\Event\EventArgs;
use Eccube\Event\TemplateEvent;
use Plugin\ProductColor\Entity\ProductProductColor;

class ProductColor
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    protected function isAuthRouteFront()
    {
        return $this->app->isGranted('ROLE_USER');
    }

    public function onRenderProductDetail(TemplateEvent $event){
        $helper = new Event\WorkPlace\FrontProductDetail();
        $helper->createTwig($event);
        
    }


    public function onRenderAdminProduct(TemplateEvent $event){
        $helper = new Event\WorkPlace\AdminProductEdit();
        $helper->createTwig($event);

        return;
        /* @var $TwigRenderService \Plugin\AdminProductListPlus\Service\TwigRenderService */
        $TwigRenderService = $this->app['adminproductlistplus.service.twigrenderservice'];
        $TwigRenderService->initTwigRenderControl($event);

        // 規格後に追加
        $search = '<div id="result_list__name--{{ Product.id }}" class="item_detail td">';

        // 価格,在庫追加
        $insert = file_get_contents('ProductColor/View/admin/product_productcolor.twig');
        $TwigRenderService->twigInsert($search, $insert, 9);

        if(Constant::VERSION != "3.0.9") {
            // 3.0.10 以降を対象
            // タグ情報追加
            $insert = file_get_contents('../app/Plugin/AdminProductListPlus/Resource/template/admin/Product/add_tag.twig');
            $TwigRenderService->twigInsert($search, $insert, 9);
        }

        $event->setSource($TwigRenderService->getContent());
    }


    public function onAdminProductEditInitialize(EventArgs $event){
        $app = $this->app;
        $request = $event->getRequest();
        $response = $event->getResponse();

        $Product = $event->getArgument('Product');

        $id = $Product->getId();
        if ($id) {
            // 編集時は初期値を取得
            $PColors = array();
            $ProductProductColor = $app['eccube.plugin.productcolor.repository.product_productcolor']->findBy(array('Product'=>$Product));
            foreach($ProductProductColor as $PColor){
                $PColors[] = $PColor->getProductColor(); 

            }
        }

        // フォームの追加
        /** @var FormInterface $builder */
        $builder = $event->getArgument('builder');

        if ('POST' === $request->getMethod()) {

        }else{
            // 初期値を設定
            $builder->get('productcolor')->setData($PColors);

        }

    }

    public function onAdminProductEditComplete(EventArgs $event){

        $app = $this->app;
        $request = $event->getRequest();
        $response = $event->getResponse();

        // if (!$response instanceof RedirectResponse) {
        //     return;
        // }

        $Product = $event->getArgument('Product');

        $id = $Product->getId();
        if ($id) {
            // 編集時は初期値を取得
            $ProductProductColor = $app['eccube.plugin.productcolor.repository.product_productcolor']->findBy(array('Product'=>$Product));
        }

        // フォームの追加
        /** @var FormInterface $builder */
        $form = $event->getArgument('form');
            $Colors = $form->get('productcolor')->getData();

        if ('POST' === $request->getMethod()) {
            // 商品タグの登録
            // 商品タグを一度クリア
            foreach ($ProductProductColor as $PColor) {
                $app['orm.em']->remove($PColor);
            }

            // 商品タグの登録
            $Colors = $form->get('productcolor')->getData();
            foreach ($Colors as $PColor) {
                $ProductColor = new ProductProductColor();
                $ProductColor
                    ->setProduct($Product)
                    ->setProductColor($PColor);
                $app['orm.em']->persist($ProductColor);
            }
            $app['orm.em']->flush();

        }

    }


    private function getHtml($request, $response, $id)
    {

        // メーカーマスタから有効なメーカー情報を取得
        $ProductColors = $this->app['eccube.plugin.productcolor.repository.productcolor']->findAll();

        if (is_null($ProductColors)) {
            $ProductColors = new \Plugin\ProductColor\Entity\ProductColor();
        }

        $ProductProductColor = null;

        if ($id) {
            // 商品メーカーマスタから設定されているなメーカー情報を取得
            $ProductProductColor = $this->app['eccube.plugin.productcolor.repository.product_productcolor']->find($id);
        }

        // 商品登録・編集画面のHTMLを取得し、DOM化
        $crawler = new Crawler($response->getContent());

        $form = $this->app['form.factory']
            ->createBuilder('admin_product')
            ->getForm();

        if ($ProductProductColor) {
            // 既に登録されている商品メーカー情報が設定されている場合、初期選択
            $form->get('productcolor')->setData($ProductProductColor->getProductColor());
        }

        $form->handleRequest($request);

        $parts = $this->app->renderView(
            'ProductColor/View/admin/product_productcolor.twig',
            array('form' => $form->createView())
        );

        // form1の最終項目に追加(レイアウトに依存
        $html = $this->getHtmlFromCrawler($crawler);

        try {
            $oldHtml = $crawler->filter('#form1 .accordion')->last()->html();//dump($oldHtml);
        $oldHtml2 = html_entity_decode($oldHtml, ENT_NOQUOTES, 'UTF-8');//dump($oldHtml2);
            $newHtml = $parts.$oldHtml2;//dump($newHtml);
            $html = str_replace($oldHtml2, $newHtml, $html);//dump($html);
        } catch (\InvalidArgumentException $e) {
            // no-op
        }

        return array($html, $form);

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
