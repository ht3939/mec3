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

namespace Plugin\SetProduct\Form\Extension\Admin;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\EntityRepository;
use Eccube\Form\DataTransformer;
use Symfony\Component\Form\CallbackTransformer;

class ProductSetProductTypeExtension extends AbstractTypeExtension
{
    private $app;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('setproduct_maker', 'entity', array(
                'label' => 'メーカー',
                'class' => 'Plugin\Maker\Entity\Maker',
                'property' => 'name',
                'required' => false,
                'empty_value' => '',
                'mapped' => false,
            ))
            ->add('setproduct_sim_flg', 'checkbox', array(
                'label' => 'SIM',
                'required' => false,
                'trim' => true,
                'value' => 1,
                'mapped' => false,
            ))
            ;

        $builder->get('setproduct_sim_flg')
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