<?php
/*
 * Copyright(c) 2015 SystemFriend Inc. All rights reserved.
 * http://ec-cube.systemfriend.co.jp/
 */

namespace Plugin\ExcludeProductPayment\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints as Assert;

class ExcludeProductPaymentTypeExtension extends AbstractTypeExtension
{
    private $app;
    private $const;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;

        /* @var $Setting \Plugin\ExcludeProductPayment\Service\ConfigService */
        $Setting = $this->app['eccube.plugin.service.epp.config'];
        $this->const = $Setting->getConst();
    }

    /**
     * 管理画面 商品登録
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return type
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ExcludeProductPayment', 'admin_exclude_product_payment', array(
                'label' => $this->const['name'] . 'の設定',
                'mapped' => false,
            ))
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {

    }

    public function getExtendedType()
    {
        return 'admin_product';
    }

}
