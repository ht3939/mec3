<?php
/*
 * Copyright(c) 2015 SystemFriend Inc. All rights reserved.
 * http://ec-cube.systemfriend.co.jp/
 */

namespace Plugin\ExcludeProductPayment\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Eccube\Form\DataTransformer;
use Symfony\Component\Form\CallbackTransformer;

class ExcludeProductPaymentType extends AbstractType
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
     * Build config type form
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return type
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* @var $Setting \Plugin\ExcludeProductPayment\Service\ConfigService */
        $Setting = $this->app['eccube.plugin.service.epp.config'];

        $builder
            ->add('excludemonthly', 'checkbox', array(
                'label' => '月額払い対象外',
                'required' => false,
                //'choices' => $Setting->getRedirectPages(false),
                //'expanded' => true,
                //'multiple' => true,
            ))
            ->add('payment_ids', 'choice', array(
                'label' => '除外する支払方法',
                'choices' => $Setting->getExcludePayment(false),
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ))
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
                $form = $event->getForm();
                $data = $form->getData();

                if (empty($data['redirect_url']) && $data['redirect_select'] == $this->const['redirect_url']) {
                    $form['redirect_url']->addError(new FormError('URLを入力してください。'));
                }
                if (empty($data['redirect_product_id']) && $data['redirect_select'] == $this->const['redirect_id']) {
                    $form['redirect_product_id']->addError(new FormError('商品IDを入力してください。'));
                }
            })
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());


        $builder->get('excludemonthly')
            ->addModelTransformer(new CallbackTransformer(
                function ($outval) {
                    // transform the string back to an array
                    return $outval?true:false;
                },
                function ($inval) {
                    // transform the array to a string
                    return $inval?1:0;
                }
            ))
        ;

        $builder->get('payment_ids')
            ->addModelTransformer(new CallbackTransformer(
                function ($outval) {
                    // transform the string back to an array
                    return is_array($outval)?$outval:unserialize($outval);
                },
                function ($inval) {
                    // transform the array to a string
                    return is_array($inval)?serialize($inval):$inval;
                }
            ))
        ;        
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Plugin\ExcludeProductPayment\Entity\ExcludeProductPayment',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_exclude_product_payment';
    }
}
