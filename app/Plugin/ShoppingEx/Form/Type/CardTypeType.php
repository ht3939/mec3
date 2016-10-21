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
namespace Plugin\ShoppingEx\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CardTypeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function __construct($config = array('cardtype' => 'VISA,JCB,AMEX,MASTER,DINERS'))
    {
        $this->config = $config;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['cardtype_options']['required'] = $options['required'];


        // required の場合は NotBlank も追加する
        if ($options['required']) {
            $options['options']['constraints'] = array_merge(array(
                new Assert\NotBlank(array()),
            ), $options['options']['constraints']);
        }
        if (!isset($options['options']['error_bubbling'])) {
            $options['options']['error_bubbling'] = $options['error_bubbling'];
        }
        // nameは呼び出しもので定義したものを使う
        if (empty($options['cardtype_name'])) {
            $options['cardtype_name'] = $builder->getName();
        }

        $builder->add('cardtype', 'choice', 
                    array_merge_recursive($options['options'], 
                        array(
                                'label' => 'カード種別',
                                'choices' => explode(',',$this->config['cardtype']),
                            )                
                        )
                    );


        $builder->setAttribute('cardtype_name', $options['cardtype_name']);




    }
    // /**
    //  * {@inheritdoc}
    //  */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $builder = $form->getConfig();
        $view->vars['cardtype_name'] = $builder->getAttribute('cardtype_name');
    //     $view->vars['cardno2'] = $builder->getAttribute('cardno2');
    //     $view->vars['cardno3'] = $builder->getAttribute('cardno3');
    //     $view->vars['cardno4'] = $builder->getAttribute('cardno4');

    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'options' => array('constraints' => array()),
            'cardtype_options' => array(),
            'cardtype_name' => '',
            'error_bubbling' => false,
            'inherit_data' => true,
            'trim' => true,
            'empty_value' => '-',
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'cardtype';
    }
}
