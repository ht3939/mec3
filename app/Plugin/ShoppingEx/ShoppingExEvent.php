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
    const SHOPPINGEX_TEXTAREA_NAME = 'plg_shoppingex';

    /**
     * @var \Eccube\Application
     */
    private $app;


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
    public function onFrontShoppingIndexInitialize(EventArgs $event){

        $app = $this->app;
        $Order = $event->getArgument('Order');
        $this->setCustomDeliveryFee($Order,true);

        //$Order = $app['eccube.productoption.service.shopping']->customOrder($Order);

        $builder = $event->getArgument('builder');
        $builder->add(
            self::SHOPPINGEX_TEXTAREA_NAME,
            'textarea',
            array(
                'required' => false,
                'label' => false,
                'mapped' => false,
                'attr' => array(
                    'placeholder' => 'コンテンツを入力してください(HTMLタグ使用可)',
                ),
            )
        );

dump($event);//die();
    }

    public function onFrontShoppingConfirmProcessing(EventArgs $event){
        $app = $this->app;
        $Order = $event->getArgument('Order');
        $this->setCustomDeliveryFee($Order,false);

    }
    public function onFrontShoppingConfirmInitialize(EventArgs $event){
        $app = $this->app;
        $Order = $event->getArgument('Order');
        $this->setCustomDeliveryFee($Order,true);


    }

    public function onFrontShoppingPaymentInitialize(EventArgs $event){

    }


    /**
     * 管理画面：カテゴリ登録画面に, カテゴリコンテンツのフォームを追加する.
     *
     * @param EventArgs $event
     */
    public function onFormInitializeAdminProductCategory(EventArgs $event)
    {
        /** @var Category $target_category */
        $TargetCategory = $event->getArgument('TargetCategory');
        $id = $TargetCategory->getId();

        $ShoppingEx = null;

        if ($id) {
            // カテゴリ編集時は初期値を取得
            $ShoppingEx = $this->app['shoppingex.repository.shoppingex']->find($id);
        }

        // カテゴリ新規登録またはコンテンツが未登録の場合
        if (is_null($ShoppingEx)) {
            $ShoppingEx = new ShoppingEx();
        }

        // フォームの追加
        /** @var FormInterface $builder */
        $builder = $event->getArgument('builder');
        $builder->add(
            self::SHOPPINGEX_TEXTAREA_NAME,
            'textarea',
            array(
                'required' => false,
                'label' => false,
                'mapped' => false,
                'attr' => array(
                    'placeholder' => 'コンテンツを入力してください(HTMLタグ使用可)',
                ),
            )
        );

        // 初期値を設定
        $builder->get(self::SHOPPINGEX_TEXTAREA_NAME)->setData($ShoppingEx->getContent());
    }

    /**
     * 管理画面：カテゴリ登録画面で、登録処理を行う.
     *
     * @param EventArgs $event
     */
    public function onAdminProductCategoryEditComplete(EventArgs $event)
    {
        /** @var Application $app */
        $app = $this->app;
        /** @var Category $target_category */
        $TargetCategory = $event->getArgument('TargetCategory');
        /** @var FormInterface $form */
        $form = $event->getArgument('form');

        // 現在のエンティティを取得
        $id = $TargetCategory->getId();
        $ShoppingEx = $app['shoppingex.repository.shoppingex']->find($id);
        if (is_null($ShoppingEx)) {
            $ShoppingEx = new ShoppingEx();
        }

        // エンティティを更新
        $ShoppingEx
            ->setId($id)
            ->setContent($form[self::SHOPPINGEX_TEXTAREA_NAME]->getData());

        // DB更新
        $app['orm.em']->persist($ShoppingEx);
        $app['orm.em']->flush($ShoppingEx);
    }


}
