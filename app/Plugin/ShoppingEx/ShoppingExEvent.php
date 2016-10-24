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

namespace Plugin\ShoppingEx;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Category;
use Eccube\Event\EventArgs;
use Eccube\Event\TemplateEvent;
use Plugin\ShoppingEx\Entity\ShoppingEx;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class ShoppingExEvent
{
    /**
     * プラグインが追加するフォーム名
     */
    const SHOPPINGEX_TEXTAREA_NAME = 'cardno';

    /**
     * @var \Eccube\Application
     */
    private $app;
    private $hasPayMonthly = false;

    /**
     * ShoppingExEvent constructor.
     *
     * @param $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 商品一覧画面にカテゴリコンテンツを表示する.
     *
     * @param TemplateEvent $event
     */
    public function onRenderProductList(TemplateEvent $event)
    {
    /*
        $parameters = $event->getParameters();

        // カテゴリIDがない場合、レンダリングしない
        if (is_null($parameters['Category'])) {
            return;
        }

        // 登録がない、もしくは空で登録されている場合、レンダリングをしない
        $Category = $parameters['Category'];
        $ShoppingEx = $this->app['shoppingex.repository.shoppingex']
            ->find($Category->getId());
        if (is_null($ShoppingEx) || $ShoppingEx->getContent() == '') {
            return;
        }

        // twigコードにカテゴリコンテンツを挿入
        $snipet = '<div class="row">{{ ShoppingEx.content | raw }}</div>';
        $search = '<div id="result_info_box"';
        $replace = $snipet.$search;
        $source = str_replace($search, $replace, $event->getSource());
        $event->setSource($source);

        // twigパラメータにカテゴリコンテンツを追加
        $parameters['ShoppingEx'] = $ShoppingEx;
        $event->setParameters($parameters);
    */
    }
    private function setCustomDeliveryFee($Order,$total_recalc = false){

        $app = $this->app;
        $deli = $Order->getDeliveryFeeTotal();//５００円固定
        $Order->setDeliveryFeeTotal(500);//５００円固定
        $total = $Order->getTotal();//５００円固定

        if($total_recalc){
            $Order->setTotal($total - $deli+500);//５００円固定
           
        }

    }
    private function onExecute(EventArgs $event){
        $app = $this->app;
dump('request');
$req = $event->getRequest();
$sec = $req->getSession();
dump($sec->get('redirect-data'));

        $Order = $event->getArgument('Order');
        $this->setCustomDeliveryFee($Order,true);


        foreach($Order->getOrderDetails() as $od){
            if($od->getProductClass()->getClassCategory2()->getId()!=10){

                $this->hasPayMonthly = true;
            }

        }
dump($event);
dump($event->getRequest()->get('shopping')['payment']);
dump($Order);


        //$Order = $app['eccube.productoption.service.shopping']->customOrder($Order);
        $paymentid = $Order->getPayment()->getId();
        $currpayment = $event->getRequest()->get('shopping')['payment'];
        if(empty($currpayment)){
            $currpayment = $paymentid;
        }
        $builder = $event->getArgument('builder');
dump($builder->get('payment')->GetData());
        //postされなくなるのでコメント
        //$builder->get('payment')->setDisabled($this->hasPayMonthly);

        if($this->hasPayMonthly){

            foreach($builder->get('payment') as $g){

                if($g->getName()=="4"){
                    $builder->get('payment')->remove("4");
                }

            }

        }
        //クレカ決済を選択した場合
        if($currpayment==5){
dump('currpayment 5');
            $ShoppingEx = $app['shoppingex.repository.shoppingex']->find($Order->getId());
            if(is_null($ShoppingEx)){
                $ShoppingEx = new ShoppingEx();

            }


            //$bud = $app['form.factory']->createBuilder('cardform',$ShoppingEx)
            //;
            $builder->add(
                        self::SHOPPINGEX_TEXTAREA_NAME,
                        'cardno',
                            array(
                            'class' => 'Plugin\ShoppingEx\Entity\ShoppingEx',
                            'property' => 'method',
                            'data' => $ShoppingEx,
                            )
                    )
            ;
                    // ->add(
                    //     'cardlimit',
                    //     'cardlimit'
                    // )
                    // ->add(
                    //     'cardtype',
                    //     'cardtype'
                    // )
                    // ->add(
                    //     'cardsec',
                    //     'cardsec'
                    // )
                    // ->add(
                    //     'tel',
                    //     'tel'
                    // );
dump($sec->get('redirect-data'));
            if($sec->get('redirect-data')){
                $form  = $builder->getForm();
                $reqbulkdata = $sec->get('redirect-data')->get('shopping');
                if(isset($reqbulkdata['cardno'])){

                    dump($sec->get('redirect-data')->get('shopping')['cardno']);
                    // 初期値を設定
                    dump($builder->get(self::SHOPPINGEX_TEXTAREA_NAME));
                    $fms = $builder->get(self::SHOPPINGEX_TEXTAREA_NAME);
                    $dat = $sec->get('redirect-data')->get('shopping')['cardno'];
                    foreach($fms as $f){
                        $f->setData($dat[$f->getName()]);
                    }
                    $form->isValid();
                    dump($builder->get(self::SHOPPINGEX_TEXTAREA_NAME));

                    
                    $ShoppingEx
                            ->setId($Order->getId())
                            ->setCardno1($dat['cardno1'])
                            ->setCardno2($dat['cardno2'])
                            ->setCardno3($dat['cardno3'])
                            ->setCardno4($dat['cardno4'])
                            ->setHolder($dat['holder'])
                            ->setCardtype($dat['cardtype'])
                            ->setCardlimitmon($dat['cardlimitmon'])
                            ->setCardlimityear($dat['cardlimityear'])
                            ->setCardsec($dat['cardsec'])
                            ;
                    $app['orm.em']->persist($ShoppingEx);
                    $app['orm.em']->flush();


                    //$sec->remove('redirect-data');
                }else{
                    $fms = $builder->get(self::SHOPPINGEX_TEXTAREA_NAME);
                    $fms->get('cardno1')->setData($ShoppingEx->getCardno1());
                    $fms->get('cardno2')->setData($ShoppingEx->getCardno2());
                    $fms->get('cardno3')->setData($ShoppingEx->getCardno3());
                    $fms->get('cardno4')->setData($ShoppingEx->getCardno4());
                    $fms->get('holder')->setData($ShoppingEx->getHolder());
                    $fms->get('cardtype')->setData($ShoppingEx->getCardtype());
                    $fms->get('cardlimitmon')->setData($ShoppingEx->getCardlimitmon());
                    $fms->get('cardlimityear')->setData($ShoppingEx->getCardlimityear());
                    $fms->get('cardsec')->setData($ShoppingEx->getCardsec());

                }

                //$form->handleRequest($sec->get('redirect-data'));
            }
        }else{
            $builder->remove('cardno');
            // $builder->remove('cardlimit');
            // $builder->remove('cardtype');
            // $builder->remove('cardsec');
            // $builder->remove('tel');
            $req = $event->getRequest();
            dump($req);
            dump($req->request);

            $dd = $req->request->get('shopping');
            unset($dd['cardno']);
            // unset($dd['cardlimit']);
            // unset($dd['cardtype']);
            // unset($dd['cardsec']);
            // unset($dd['tel']);
            $req->request->set('shopping',$dd);
            
            dump($req->request);
//$currpayment = $event->getRequest()->get('shopping');

        }

    }
    public function onFrontShoppingIndexInitialize(EventArgs $event){
        dump('index init');
        dump($event->getRequest());
        $this->onExecute($event);


    }

    public function onFrontShoppingConfirmInitialize(EventArgs $event){
        $app = $this->app;
        dump('confirm init');
        $this->onExecute($event);
        dump('confirm check pre');

        $builder = $event->getArgument('builder');
        $form = $builder->getForm();
        dump($builder);

        dump('confirm check handle');
        dump($event->getRequest());
        $request = $event->getRequest();
        $form->handleRequest($request);
        dump($form);
        dump('confirm check valid');

        if (!$form->isValid()) {
        $Order = $event->getArgument('Order');
         dump('confirm check');
           //$app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_PAYMENT_COMPLETE, $event);
            $data = $form->getData();
            $payment = $data['payment'];
            $message = $data['message'];

            $Order->setPayment($payment);
            $Order->setPaymentMethod($payment->getMethod());
            $Order->setMessage($message);
            $Order->setCharge($payment->getCharge());

            // 合計金額の再計算
            $Order = $app['eccube.service.shopping']->getAmount($Order);

            // 受注関連情報を最新状態に更新
            $app['orm.em']->flush();

            dump('confirm redirect');
            $session = $request->getSession();
            $session->set('redirect-data',$request->request);
            $event->setResponse($app->redirect($app->url('shopping')));

        }
    }
    public function onFrontShoppingConfirmProcessing(EventArgs $event){
        dump('confirm process');
        //$this->onExecute($event);


        $app = $this->app;
        $Order = $event->getArgument('Order');
        $this->setCustomDeliveryFee($Order,false);

dump(
$event->getArgument('builder')
);
die();

    }

    public function onFrontShoppingConfirmComplete(EventArgs $event){
        //セッションから消す
        //$session->set('redirect-data',$request->request);
    }

    public function onFrontShoppingPaymentInitialize(EventArgs $event){
        $app = $this->app;
        dump('payment init');
        $this->onExecute($event);

        dump('payment check pre');

        $builder = $event->getArgument('builder');
        $form = $builder->getForm();
        dump($builder);

        dump('payment check handle');
        dump($event->getRequest());
        $request = $event->getRequest();
        $form->handleRequest($request);
        dump($form);
        dump('payment check valid');

        if (!$form->isValid()) {
        $Order = $event->getArgument('Order');
         dump('payment check');
           //$app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_PAYMENT_COMPLETE, $event);
            $data = $form->getData();
            $payment = $data['payment'];
            $message = $data['message'];

            $Order->setPayment($payment);
            $Order->setPaymentMethod($payment->getMethod());
            $Order->setMessage($message);
            $Order->setCharge($payment->getCharge());

            // 合計金額の再計算
            $Order = $app['eccube.service.shopping']->getAmount($Order);

            // 受注関連情報を最新状態に更新
            $app['orm.em']->flush();

            dump('payment redirect');
            $session = $request->getSession();
            $session->set('redirect-data',$request->request);
            $event->setResponse($app->redirect($app->url('shopping')));

        }


    }

    public function onFrontShoppingPaymentComplete(EventArgs $event){
        dump('payment complete');


    }



    public function onFrontShoppingDeliveryInitialize(EventArgs $event){
        $app = $this->app;
        dump('delivery init');

    }
    public function onFrontShoppingDeliveryComplete(EventArgs $event){

    }
    public function onFrontShoppingShippingChangeInitialize(EventArgs $event){
        $app = $this->app;
        dump('shippingChange init');

    }
    public function onFrontShoppingShippingComplete(EventArgs $event){


    }
    public function onFrontShoppingShippingEditChangeInitialize(EventArgs $event){
        $app = $this->app;
        dump('shippingEditChange init');
        $this->onExecute($event);

        dump('shippingEditChange check pre');

        $id = $event->getArgument('id');

        $builder = $event->getArgument('builder');
        $form = $builder->getForm();
        dump($builder);

        dump('shippingEditChange check handle');
        dump($event->getRequest());
        $request = $event->getRequest();
        $form->handleRequest($request);
        dump($form);
        dump('shippingEditChange check valid');

        if (!$form->isValid()) {
        $Order = $event->getArgument('Order');
         dump('shippingEditChange check');
           //$app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_PAYMENT_COMPLETE, $event);
            $data = $form->getData();
            $payment = $data['payment'];
            $message = $data['message'];

            $Order->setPayment($payment);
            $Order->setPaymentMethod($payment->getMethod());
            $Order->setMessage($message);
            $Order->setCharge($payment->getCharge());

            // 合計金額の再計算
            $Order = $app['eccube.service.shopping']->getAmount($Order);

            // 受注関連情報を最新状態に更新
            $app['orm.em']->flush();

            dump('shippingEditChange redirect');
            $session = $request->getSession();
            $session->set('redirect-data',$request->request);
            $event->setResponse(
                $app->redirect($app->url('shopping_shipping_edit', array('id' => $id)))
                );

        }


    }
    public function onFrontShoppingShippingEditInitialize(EventArgs $event){
        $app = $this->app;
        dump('shippingEdit init');

    }
    public function onFrontShoppingShippingEditComplete(EventArgs $event){

    }
    public function onFrontShoppingShippingMultipleChangeInitialize(EventArgs $event){
        $app = $this->app;
        dump('shippingmultipleChange init');
        $this->onExecute($event);

        dump('shippingmultipleChange check pre');

        // $id = $event->getArgument('id');

        $builder = $event->getArgument('builder');
        $form = $builder->getForm();
        dump($builder);

        dump('shippingmultipleChange check handle');
        dump($event->getRequest());
        $request = $event->getRequest();
        $form->handleRequest($request);
        dump($form);
        dump('shippingmultipleChange check valid');

        if (!$form->isValid()) {
        $Order = $event->getArgument('Order');
         dump('shippingmultipleChange check');
           //$app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_PAYMENT_COMPLETE, $event);
            $data = $form->getData();
            $payment = $data['payment'];
            $message = $data['message'];

            $Order->setPayment($payment);
            $Order->setPaymentMethod($payment->getMethod());
            $Order->setMessage($message);
            $Order->setCharge($payment->getCharge());

            // 合計金額の再計算
            $Order = $app['eccube.service.shopping']->getAmount($Order);

            // 受注関連情報を最新状態に更新
            $app['orm.em']->flush();

            dump('shippingmultipleChange redirect');
            $session = $request->getSession();
            $session->set('redirect-data',$request->request);
            $event->setResponse(
                $app->redirect($app->url('shopping_shipping_multiple'))
                );

        }

    }
    public function onFrontShoppingShippingMultipleInitialize(EventArgs $event){
        $app = $this->app;
        dump('shippingmultiple init');

    }
    public function onFrontShoppingShippingMultipleComplete(EventArgs $event){

    }
    public function onFrontShoppingShippingMultipleEditInitialize(EventArgs $event){
        $app = $this->app;
        dump('shippingmultipleEdit init');

    }
    public function onFrontShoppingShippingMultipleEditComplete(EventArgs $event){

    }


}
