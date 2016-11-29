<?php
/*
 * Copyright(c) 2016 SYSTEM_KD
 */

namespace Plugin\DirectCartIn;


use Eccube\Event\TemplateEvent;
use Eccube\Event\EventArgs;
use Eccube\Common\Constant;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class DirectCartIn
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
        $this->const = $this->app['config']['DirectCartIn']['const'];
    }

    /**
     * 商品詳細ページのトークンを取得
     *
     */
    private function getProductDetailToken() {
        return $this->app['form.csrf_provider']->getToken('Eccube\Form\Type\AddCartType')->getValue();
    }

    /**
     * 商品一覧テンプレート
     *
     * @param TemplateEvent $event
     */
    public function onProductListTwig(TemplateEvent $event)
    {

        // Token設定
        $parameters = $event->getParameters();
        $token = $token = $this->getProductDetailToken();
        $parameters['plg_token'] = $token;

        $event->setParameters($parameters);

        /* @var $TwigRenderService \Plugin\DirectCartIn\Service\TwigRenderService */
        $TwigRenderService = $this->app['directcartin.service.twigrenderservice'];
        $TwigRenderService->initTwigRenderControl($event);

        // カートボタン追加
        $search = '<a href="{{ url(\'product_detail\', {\'id\': Product.id}) }}">';
        $view = 'DirectCartIn/Resource/template/Product/add_list.twig';
        $insert = $TwigRenderService->readTwigFile($view);
        $TwigRenderService->twigInsert($search, $insert, 22);

        // JS追加
        $search = '{% block javascript %}';
        $view = 'DirectCartIn/Resource/template/Product/add_list_js.twig';
        $insert = $TwigRenderService->readTwigFile($view);
        $TwigRenderService->twigInsert($search, $insert);

        // Toke保持用のhidden追加
        $search = '{% block main %}';
        $insert = '<input type="hidden" name="detail_token" id="detail_token" value="{{ plg_token }}"/>';
        $TwigRenderService->twigInsert($search, $insert);

        $event->setSource($TwigRenderService->getContent());

    }

    /**
     * 商品詳細初期処理
     *
     * @param EventArgs $event
     */
    public function onFrontProductDetailInitialize(EventArgs $event)
    {

        $builder = $event->getArgument('builder');
        $request = $event->getRequest();

        $mode_ex = $request->get('mode_ex');

        if(empty($mode_ex)) {
            return;
        }

        // mode_exを拡張
        $builder->add('mode_ex', 'hidden', array(
                'data' => $mode_ex,
                'mapped' => false,
            )
        );
    }

    /**
     * カート追加完了時
     *
     * @param EventArgs $event
     */
    public function onFrontProductDetailComplete(EventArgs $event)
    {

        $app = $this->app;

        $request = $event->getRequest();
        if($request->get('mode_ex') != 'direct_cart_in') {
            return;
        }

        /* @var $Product \Eccube\Entity\Product */
        $Product = $event->getArgument('Product');

        $arrResult = array();

        // エラーチェック
        $err = $app['session']->getFlashBag()->get('eccube.front.request.error');

        if(count($err) > 0) {

            $msg = $app['translator']->trans($err[0], array('%product%' => $Product->getName()));

            $arrResult['ret'] = 0;
            $arrResult['msg'] = $msg;

        } else {
            $arrResult['ret'] = 1;
        }

        /* @var $Cart \Eccube\Entity\Cart */
        $Cart = $app['eccube.service.cart']->getCart();
        $total_price = number_format($Cart->getTotalPrice());
        $total_quantity = $Cart->getTotalQuantity();

        $arrResult['new_token'] = $this->getProductDetailToken();
        $arrResult['total_price'] = $total_price;
        $arrResult['total_quantity'] = $total_quantity;

        $response = new Response(json_encode($arrResult));
        $response->headers->set('Content-Type', 'application/json');

        $event->setResponse($response);
    }

    /**
     * 商品一覧レスポンス フック
     *
     * @param GetResponseEvent $event
     */
    public function onEccubeEventRouteProductListResponse(FilterResponseEvent $event)
    {

        /* @var $TwigRenderService \Plugin\DirectCartIn\Service\TwigRenderService */
        $TwigRenderService = $this->app['directcartin.service.twigrenderservice'];

        $html = $event->getResponse()->getContent();

        $search = '<div class="overlay"></div>';
        $view = 'DirectCartIn/Resource/template/Product/list_ex.twig';
        $replace = $TwigRenderService->readTwigFile($view);
        $html = str_replace($search, $search.$replace, $html);

        $event->getResponse()->setContent($html);
    }

}
