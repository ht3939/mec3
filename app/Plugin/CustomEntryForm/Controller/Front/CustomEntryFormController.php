<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Plugin\CustomEntryForm\Controller\Front;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception as HttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;
use Eccube\Util\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Exception\CartException;

class CustomEntryFormController extends AbstractController
{
    /**
     * @var string 非会員用セッションキー
     */
    private $sessionKey = 'eccube.front.shopping.nonmember';

    /**
     * @var string 非会員用セッションキー
     */
    private $sessionCustomerAddressKey = 'eccube.front.shopping.nonmember.customeraddress';

    /**
     * @var string 複数配送警告メッセージ
     */
    private $sessionMultipleKey = 'eccube.front.shopping.multiple';

    /**
     * @var string 受注IDキー
     */
    private $sessionOrderKey = 'eccube.front.shopping.order.id';

    public function index(Application $app, Request $request,$id=null)
    {
        $Cart = $app['eccube.service.cart']->getCart();

        // FRONT_CART_INDEX_INITIALIZE
        $event = new EventArgs(
            array(),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_CART_INDEX_INITIALIZE, $event);

        /* @var $BaseInfo \Eccube\Entity\BaseInfo */
        /* @var $Cart \Eccube\Entity\Cart */
        $BaseInfo = $app['eccube.repository.base_info']->get();

        $isDeliveryFree = false;
        $least = 0;
        $quantity = 0;
        if ($BaseInfo->getDeliveryFreeAmount()) {
            if ($BaseInfo->getDeliveryFreeAmount() <= $Cart->getTotalPrice()) {
                // 送料無料（金額）を超えている
                $isDeliveryFree = true;
            } else {
                $least = $BaseInfo->getDeliveryFreeAmount() - $Cart->getTotalPrice();
            }
        }

        if ($BaseInfo->getDeliveryFreeQuantity()) {
            if ($BaseInfo->getDeliveryFreeQuantity() <= $Cart->getTotalQuantity()) {
                // 送料無料（個数）を超えている
                $isDeliveryFree = true;
            } else {
                $quantity = $BaseInfo->getDeliveryFreeQuantity() - $Cart->getTotalQuantity();
            }
        }

        // FRONT_CART_INDEX_COMPLETE
        $event = new EventArgs(
            array(),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_CART_INDEX_COMPLETE, $event);

        if ($event->hasResponse()) {
            return $event->getResponse();
        }


        $cartService = $app['eccube.service.cart'];
        /*
                // カートチェック
                if (!$cartService->isLocked()) {
                    // カートが存在しない、カートがロックされていない時はエラー
                    log_info('カートが存在しません');
                    return $app->redirect($app->url('cart'));
                }

                // ログイン済みの場合は, 購入画面へリダイレクト.
                if ($app->isGranted('ROLE_USER')) {
                    return $app->redirect($app->url('shopping'));
                }
        */
        // カートチェック
        if (count($cartService->getCart()->getCartItems()) <= 0) {
            // カートが存在しない時はエラー
            log_info('カートに商品が入っていないためショッピングカート画面にリダイレクト');
            return $app->redirect($app->url('cart'));
        }

        $builder = $app['form.factory']->createBuilder('nonmember');

        //初期値表示用
        $form = $builder->getForm();

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'form'=>$form,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_NONMEMBER_INITIALIZE, $event);
        if($event->getArgument('form')){
            $form = $event->getArgument('form');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            log_info('非会員お客様情報登録開始');

            $data = $form->getData();
            $Customer = new Customer();
            $Customer
                ->setName01($data['name01'])
                ->setName02($data['name02'])
                ->setKana01($data['kana01'])
                ->setKana02($data['kana02'])
                ->setSex($data['sex'])

                ->setCompanyName($data['company_name'])
                ->setEmail($data['email'])
                ->setTel01($data['tel01'])
                ->setTel02($data['tel02'])
                ->setTel03($data['tel03'])
                ->setZip01($data['zip01'])
                ->setZip02($data['zip02'])
                ->setZipCode($data['zip01'].$data['zip02'])
                ->setPref($data['pref'])
                ->setAddr01($data['addr01'])
                ->setAddr02($data['addr02']);

            // 非会員複数配送用
            $CustomerAddress = new CustomerAddress();
            $CustomerAddress
                ->setCustomer($Customer)
                ->setName01($data['name01'])
                ->setName02($data['name02'])
                ->setKana01($data['kana01'])
                ->setKana02($data['kana02'])
                ->setCompanyName($data['company_name'])
                ->setTel01($data['tel01'])
                ->setTel02($data['tel02'])
                ->setTel03($data['tel03'])
                ->setZip01($data['zip01'])
                ->setZip02($data['zip02'])
                ->setZipCode($data['zip01'].$data['zip02'])
                ->setPref($data['pref'])
                ->setAddr01($data['addr01'])
                ->setAddr02($data['addr02'])
                ->setDelFlg(Constant::DISABLED);
            $Customer->addCustomerAddress($CustomerAddress);

            // 受注情報を取得
            $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);

            // 初回アクセス(受注データがない)の場合は, 受注情報を作成
            if (is_null($Order)) {
                // 受注情報を作成
                try {
                    // 受注情報を作成
                    $Order = $app['eccube.service.shopping']->createOrder($Customer);
                } catch (CartException $e) {
                    $app->addRequestError($e->getMessage());
                    return $app->redirect($app->url('cart'));
                }
            }

            // 非会員用セッションを作成
            $nonMember = array();
            $nonMember['customer'] = $Customer;
            $nonMember['pref'] = $Customer->getPref()->getId();
            $nonMember['sex'] = $Customer->getSex();
            $app['session']->set($this->sessionKey, $nonMember);

            $customerAddresses = array();
            $customerAddresses[] = $CustomerAddress;
            $app['session']->set($this->sessionCustomerAddressKey, serialize($customerAddresses));

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Order' => $Order,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_NONMEMBER_COMPLETE, $event);

            if ($event->getResponse() !== null) {
                return $event->getResponse();
            }

            log_info('非会員お客様情報登録完了', array($Order->getId()));

            return $app->redirect($app->url('shopping'));
        }

        return $app->render(
            'CustomEntryForm/Resource/template/default/CustomEntryForm/index.twig',
            array(
                'Cart' => $Cart,
                'least' => $least,
                'quantity' => $quantity,
                'is_delivery_free' => $isDeliveryFree,
                'form' => $form->createView(),

            )
        );
    }
    /**
     * カートをロック状態に設定し、購入確認画面へ遷移する.
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function buystep(Application $app, Request $request)
    {
        // FRONT_CART_BUYSTEP_INITIALIZE
        $event = new EventArgs(
            array(),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_CART_BUYSTEP_INITIALIZE, $event);

        $app['eccube.service.cart']->lock();
        $app['eccube.service.cart']->save();

        // FRONT_CART_BUYSTEP_COMPLETE
        $event = new EventArgs(
            array(),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_CART_BUYSTEP_COMPLETE, $event);

        if ($event->hasResponse()) {
            return $event->getResponse();
        }

        return $app->redirect($app->url('plugin_customentryform_formentry'));
    }

    /**
     * 購入画面表示
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function shopping_index(Application $app, Request $request)
    {
        $cartService = $app['eccube.service.cart'];

        // カートチェック
        if (!$cartService->isLocked()) {
            log_info('カートが存在しません');
            // カートが存在しない、カートがロックされていない時はエラー
            return $app->redirect($app->url('cart'));
        }

        // カートチェック
        if (count($cartService->getCart()->getCartItems()) <= 0) {
            log_info('カートに商品が入っていないためショッピングカート画面にリダイレクト');
            // カートが存在しない時はエラー
            return $app->redirect($app->url('cart'));
        }

        // 登録済みの受注情報を取得
        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);

        // 初回アクセス(受注情報がない)の場合は, 受注情報を作成
        if (is_null($Order)) {
            // 未ログインの場合, ログイン画面へリダイレクト.
            if (!$app->isGranted('IS_AUTHENTICATED_FULLY')) {
                // 非会員でも一度会員登録されていればショッピング画面へ遷移
                $Customer = $app['eccube.service.shopping']->getNonMember($this->sessionKey);

                if (is_null($Customer)) {
                    log_info('未ログインのためログイン画面にリダイレクト');
                    return $app->redirect($app->url('shopping_login'));
                }
            } else {
                $Customer = $app->user();
            }

            try {
                // 受注情報を作成
                $Order = $app['eccube.service.shopping']->createOrder($Customer);
            } catch (CartException $e) {
                log_error('初回受注情報作成エラー', array($e->getMessage()));
                $app->addRequestError($e->getMessage());
                return $app->redirect($app->url('cart'));
            }

            // セッション情報を削除
            $app['session']->remove($this->sessionOrderKey);
            $app['session']->remove($this->sessionMultipleKey);
        }

        // 受注関連情報を最新状態に更新
        $app['orm.em']->refresh($Order);

        // form作成
        $builder = $app['eccube.service.shopping']->getShippingFormBuilder($Order);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Order' => $Order,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        if ($Order->getTotalPrice() < 0) {
            // 合計金額がマイナスの場合、エラー
            log_info('受注金額マイナスエラー', array($Order->getId()));
            $message = $app->trans('shopping.total.price', array('totalPrice' => number_format($Order->getTotalPrice())));
            $app->addError($message);

            return $app->redirect($app->url('shopping_error'));
        }

        // 複数配送の場合、エラーメッセージを一度だけ表示
        if (!$app['session']->has($this->sessionMultipleKey)) {
            if (count($Order->getShippings()) > 1) {
                $app->addRequestError('shopping.multiple.delivery');
            }
            $app['session']->set($this->sessionMultipleKey, 'multiple');
        }

        return $app->render('Shopping/index.twig', array(
            'form' => $form->createView(),
            'Order' => $Order,
        ));
    }

    /**
     * 購入処理
     */
    public function confirm(Application $app, Request $request)
    {
        $cartService = $app['eccube.service.cart'];

        // カートチェック
        if (!$cartService->isLocked()) {
            // カートが存在しない、カートがロックされていない時はエラー
            log_info('カートが存在しません');
            return $app->redirect($app->url('cart'));
        }

        $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);
        if (!$Order) {
            log_info('購入処理中の受注情報がないため購入エラー');
            $app->addError('front.shopping.order.error');
            return $app->redirect($app->url('shopping_error'));
        }

        if ('POST' !== $request->getMethod()) {
            return $app->redirect($app->url('cart'));
        }

        // form作成
        $builder = $app['eccube.service.shopping']->getShippingFormBuilder($Order);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Order' => $Order,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_CONFIRM_INITIALIZE, $event);
        
        $form = $builder->getForm();

        $form->handleRequest($request);
        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            log_info('購入処理開始', array($Order->getId()));

            // トランザクション制御
            $em = $app['orm.em'];
            $em->getConnection()->beginTransaction();
            try {

                // お問い合わせ、配送時間などのフォーム項目をセット
                $app['eccube.service.shopping']->setFormData($Order, $data);
                // 購入処理
                $app['eccube.service.shopping']->processPurchase($Order);

                $em->flush();
                $em->getConnection()->commit();

                log_info('購入処理完了', array($Order->getId()));

            } catch (ShoppingException $e) {

                log_error('購入エラー', array($e->getMessage()));

                $em->getConnection()->rollback();

                $app->log($e);
                $app->addError($e->getMessage());

                return $app->redirect($app->url('shopping_error'));
            } catch (\Exception $e) {

                log_error('予期しないエラー', array($e->getMessage()));

                $em->getConnection()->rollback();

                $app->log($e);

                $app->addError('front.shopping.system.error');
                return $app->redirect($app->url('shopping_error'));
            }

            // カート削除
            $app['eccube.service.cart']->clear()->save();

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Order' => $Order,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_CONFIRM_PROCESSING, $event);

            if ($event->getResponse() !== null) {
                log_info('イベントレスポンス返却', array($Order->getId()));
                return $event->getResponse();
            }

            // 受注IDをセッションにセット
            $app['session']->set($this->sessionOrderKey, $Order->getId());

            // メール送信
            $MailHistory = $app['eccube.service.shopping']->sendOrderMail($Order);

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Order' => $Order,
                    'MailHistory' => $MailHistory,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_CONFIRM_COMPLETE, $event);

            if ($event->getResponse() !== null) {
                log_info('イベントレスポンス返却', array($Order->getId()));
                return $event->getResponse();
            }

            // 完了画面表示
            return $app->redirect($app->url('shopping_complete'));
        }

        log_info('購入チェックエラー', array($Order->getId()));

        return $app->render('Shopping/index.twig', array(
            'form' => $form->createView(),
            'Order' => $Order,
        ));
    }


    /**
     * 購入完了画面表示
     */
    public function complete(Application $app, Request $request)
    {
        // 受注IDを取得
        $orderId = $app['session']->get($this->sessionOrderKey);

        $event = new EventArgs(
            array(
                'orderId' => $orderId,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_COMPLETE_INITIALIZE, $event);

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        // 受注IDセッションを削除
        $app['session']->remove($this->sessionOrderKey);

        log_info('購入処理完了', array($orderId));

        return $app->render('Shopping/complete.twig', array(
            'orderId' => $orderId,
        ));
    }

    /**
     * 非会員処理
     */
    public function nonmember(Application $app, Request $request)
    {
        $cartService = $app['eccube.service.cart'];

        // カートチェック
        if (!$cartService->isLocked()) {
            // カートが存在しない、カートがロックされていない時はエラー
            log_info('カートが存在しません');
            return $app->redirect($app->url('cart'));
        }

        // ログイン済みの場合は, 購入画面へリダイレクト.
        if ($app->isGranted('ROLE_USER')) {
            return $app->redirect($app->url('shopping'));
        }

        // カートチェック
        if (count($cartService->getCart()->getCartItems()) <= 0) {
            // カートが存在しない時はエラー
            log_info('カートに商品が入っていないためショッピングカート画面にリダイレクト');
            return $app->redirect($app->url('cart'));
        }

        $builder = $app['form.factory']->createBuilder('nonmember');

        //初期値表示用
        $form = $builder->getForm();

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'form'=>$form,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_NONMEMBER_INITIALIZE, $event);
        if($event->getArgument('form')){
            $form = $event->getArgument('form');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            log_info('非会員お客様情報登録開始');

            $data = $form->getData();
            $Customer = new Customer();
            $Customer
                ->setName01($data['name01'])
                ->setName02($data['name02'])
                ->setKana01($data['kana01'])
                ->setKana02($data['kana02'])
                ->setSex($data['sex'])

                ->setCompanyName($data['company_name'])
                ->setEmail($data['email'])
                ->setTel01($data['tel01'])
                ->setTel02($data['tel02'])
                ->setTel03($data['tel03'])
                ->setZip01($data['zip01'])
                ->setZip02($data['zip02'])
                ->setZipCode($data['zip01'].$data['zip02'])
                ->setPref($data['pref'])
                ->setAddr01($data['addr01'])
                ->setAddr02($data['addr02']);

            // 非会員複数配送用
            $CustomerAddress = new CustomerAddress();
            $CustomerAddress
                ->setCustomer($Customer)
                ->setName01($data['name01'])
                ->setName02($data['name02'])
                ->setKana01($data['kana01'])
                ->setKana02($data['kana02'])
                ->setCompanyName($data['company_name'])
                ->setTel01($data['tel01'])
                ->setTel02($data['tel02'])
                ->setTel03($data['tel03'])
                ->setZip01($data['zip01'])
                ->setZip02($data['zip02'])
                ->setZipCode($data['zip01'].$data['zip02'])
                ->setPref($data['pref'])
                ->setAddr01($data['addr01'])
                ->setAddr02($data['addr02'])
                ->setDelFlg(Constant::DISABLED);
            $Customer->addCustomerAddress($CustomerAddress);

            // 受注情報を取得
            $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);

            // 初回アクセス(受注データがない)の場合は, 受注情報を作成
            if (is_null($Order)) {
                // 受注情報を作成
                try {
                    // 受注情報を作成
                    $Order = $app['eccube.service.shopping']->createOrder($Customer);
                } catch (CartException $e) {
                    $app->addRequestError($e->getMessage());
                    return $app->redirect($app->url('cart'));
                }
            }

            // 非会員用セッションを作成
            $nonMember = array();
            $nonMember['customer'] = $Customer;
            $nonMember['pref'] = $Customer->getPref()->getId();
            $nonMember['sex'] = $Customer->getSex();
            $app['session']->set($this->sessionKey, $nonMember);

            $customerAddresses = array();
            $customerAddresses[] = $CustomerAddress;
            $app['session']->set($this->sessionCustomerAddressKey, serialize($customerAddresses));

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Order' => $Order,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_NONMEMBER_COMPLETE, $event);

            if ($event->getResponse() !== null) {
                return $event->getResponse();
            }

            log_info('非会員お客様情報登録完了', array($Order->getId()));

            return $app->redirect($app->url('shopping'));
        }

        return $app->render('Shopping/nonmember.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * お客様情報の変更(非会員)
     */
    public function customer(Application $app, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            try {

                log_info('非会員お客様情報変更処理開始');

                $data = $request->request->all();

                // 入力チェック
                $errors = $this->customerValidation($app, $data);

                foreach ($errors as $error) {
                    if ($error->count() != 0) {
                        log_info('非会員お客様情報変更入力チェックエラー');
                        $response = new Response(json_encode('NG'), 400);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                }

                $pref = $app['eccube.repository.master.pref']->findOneBy(array('name' => $data['customer_pref']));
                if (!$pref) {
                    log_info('非会員お客様情報変更入力チェックエラー');
                    $response = new Response(json_encode('NG'), 400);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }

                $Order = $app['eccube.service.shopping']->getOrder($app['config']['order_processing']);
                if (!$Order) {
                    log_info('カートが存在しません');
                    $app->addError('front.shopping.order.error');
                    return $app->redirect($app->url('shopping_error'));
                }

                $Order
                    ->setName01($data['customer_name01'])
                    ->setName02($data['customer_name02'])
                    ->setCompanyName($data['customer_company_name'])
                    ->setTel01($data['customer_tel01'])
                    ->setTel02($data['customer_tel02'])
                    ->setTel03($data['customer_tel03'])
                    ->setZip01($data['customer_zip01'])
                    ->setZip02($data['customer_zip02'])
                    ->setZipCode($data['customer_zip01'].$data['customer_zip02'])
                    ->setPref($pref)
                    ->setAddr01($data['customer_addr01'])
                    ->setAddr02($data['customer_addr02'])
                    ->setEmail($data['customer_email']);

                // 配送先を更新
                $app['orm.em']->flush();

                // 受注関連情報を最新状態に更新
                $app['orm.em']->refresh($Order);

                $event = new EventArgs(
                    array(
                        'Order' => $Order,
                        'data' => $data,
                    ),
                    $request
                );
                $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_CUSTOMER_INITIALIZE, $event);

                log_info('非会員お客様情報変更処理完了', array($Order->getId()));
                $response = new Response(json_encode('OK'));
                $response->headers->set('Content-Type', 'application/json');
            } catch (\Exception $e) {
                log_error('予期しないエラー', array($e->getMessage()));
                $app['monolog']->error($e);

                $response = new Response(json_encode('NG'), 500);
                $response->headers->set('Content-Type', 'application/json');
            }

            return $response;
        }
    }

    /**
     * 購入エラー画面表示
     */
    public function shoppingError(Application $app, Request $request)
    {

        $event = new EventArgs(
            array(),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_SHOPPING_SHIPPING_ERROR_COMPLETE, $event);

        if ($event->getResponse() !== null) {
            return $event->getResponse();
        }

        return $app->render('Shopping/shopping_error.twig');
    }

    /**
     * 非会員でのお客様情報変更時の入力チェック
     *
     * @param Application $app
     * @param array $data リクエストパラメータ
     * @return array
     */
    private function customerValidation(Application $app, array $data)
    {
        // 入力チェック
        $errors = array();

        $errors[] = $app['validator']->validateValue($data['customer_name01'], array(
            new Assert\NotBlank(),
            new Assert\Length(array('max' => $app['config']['name_len'],)),
            new Assert\Regex(array('pattern' => '/^[^\s ]+$/u', 'message' => 'form.type.name.firstname.nothasspace'))
        ));

        $errors[] = $app['validator']->validateValue($data['customer_name02'], array(
            new Assert\NotBlank(),
            new Assert\Length(array('max' => $app['config']['name_len'],)),
            new Assert\Regex(array('pattern' => '/^[^\s ]+$/u', 'message' => 'form.type.name.firstname.nothasspace'))
        ));

        $errors[] = $app['validator']->validateValue($data['customer_company_name'], array(
            new Assert\Length(array('max' => $app['config']['stext_len'])),
        ));

        $errors[] = $app['validator']->validateValue($data['customer_tel01'], array(
            new Assert\NotBlank(),
            new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
            new Assert\Length(array('max' => $app['config']['tel_len'], 'min' => $app['config']['tel_len_min'])),
        ));

        $errors[] = $app['validator']->validateValue($data['customer_tel02'], array(
            new Assert\NotBlank(),
            new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
            new Assert\Length(array('max' => $app['config']['tel_len'], 'min' => $app['config']['tel_len_min'])),
        ));

        $errors[] = $app['validator']->validateValue($data['customer_tel03'], array(
            new Assert\NotBlank(),
            new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
            new Assert\Length(array('max' => $app['config']['tel_len'], 'min' => $app['config']['tel_len_min'])),
        ));

        $errors[] = $app['validator']->validateValue($data['customer_zip01'], array(
            new Assert\NotBlank(),
            new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
            new Assert\Length(array('min' => $app['config']['zip01_len'], 'max' => $app['config']['zip01_len'])),
        ));

        $errors[] = $app['validator']->validateValue($data['customer_zip02'], array(
            new Assert\NotBlank(),
            new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
            new Assert\Length(array('min' => $app['config']['zip02_len'], 'max' => $app['config']['zip02_len'])),
        ));

        $errors[] = $app['validator']->validateValue($data['customer_addr01'], array(
            new Assert\NotBlank(),
            new Assert\Length(array('max' => $app['config']['address1_len'])),
        ));

        $errors[] = $app['validator']->validateValue($data['customer_addr02'], array(
            new Assert\NotBlank(),
            new Assert\Length(array('max' => $app['config']['address2_len'])),
        ));

        $errors[] = $app['validator']->validateValue($data['customer_email'], array(
            new Assert\NotBlank(),
            new Assert\Email(array('strict' => true)),
        ));

        return $errors;
    }
}
