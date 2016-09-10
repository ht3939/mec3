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
        $const = $this->const;

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
        $insert = file_get_contents($const['TEMPLATE_PATH']."Product/add_list.twig");
        $TwigRenderService->twigInsert($search, $insert, 22);

        // JS追加
        $search = '{% block javascript %}';
        $insert = file_get_contents($const['TEMPLATE_PATH']."Product/add_list_js.twig");
        $TwigRenderService->twigInsert($search, $insert);

        // Toke保持用のhidden追加
        $search = '{% block main %}';
        $insert = '<input type="hidden" id="detail_token" value="{{ plg_token }}"/>';
        $TwigRenderService->twigInsert($search, $insert);

        $event->setSource($TwigRenderService->getContent());

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
        if($request->get('mode') != 'direct_cart_in') {
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

        $html = $event->getResponse()->getContent();

        $const = $this->const;

        $search = '<div class="overlay"></div>';
        $replace = file_get_contents($const['TEMPLATE_PATH'].'Product/list_ex.twig');
        $html = str_replace($search, $search.$replace, $html);

        $event->getResponse()->setContent($html);
    }

}
