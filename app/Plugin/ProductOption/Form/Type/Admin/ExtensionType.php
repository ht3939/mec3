<?php
/*
 * Plugin Name : ProductOption
 *
 * Copyright (C) 2015 BraTech Co., Ltd. All Rights Reserved.
 * http://www.bratech.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ProductOption\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Eccube\Form\DataTransformer;
use Symfony\Component\Form\CallbackTransformer;

class ExtensionType extends AbstractType
{

    public $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;

        $builder
            // ->add('option_id', 'hidden', array(
            //     'property_path' => 'Option.id',
            // ))
            ->add('descdisp_flg', 'checkbox', array(
                'label' => '説明文表示',
                'required' => false,
                'trim' => true,
                // 'empty_value' => 0,
                // 'value' => 1,
                'mapped' => false,
            ))
            ->add('exclude_payment_flg', 'checkbox', array(
                'label' => '金額除外',
                'required' => false,
                'trim' => true,
                'mapped' => false,
            ))
                            // ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber())
            ;

        // $builder->get('descdisp_flg')
        //     ->addModelTransformer(new CallbackTransformer(
        //         function ($outval) {
        //             // transform the string back to an array
        //             return $outval?true:false;
        //         },
        //         function ($inval) {
        //             // transform the array to a string
        //             return $inval?1:0;
        //         }
        //     ))
        // ;

    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Plugin\ProductOption\Entity\Extension',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'extension';
        // return 'admin_product_option';
    }

}
