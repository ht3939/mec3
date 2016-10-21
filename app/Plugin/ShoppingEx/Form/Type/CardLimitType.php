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

class CardLimitType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function __construct($config = array(
        'cardlimit_mon' => '01,02,03,04,05,06,07,08,09,11,12',
        'cardlimit_year' => '2016,2017,2018,2019,2020,2021,2022,2023,2024,2025,2026,2027,2028'
        )
    )
    {
        $this->config = $config;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['cardlimit1_options']['required'] = $options['required'];
        $options['cardlimit2_options']['required'] = $options['required'];


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
        if (empty($options['cardlimit1_name'])) {
            $options['cardlimit1_name'] = $builder->getName().'1';
        }
        if (empty($options['cardno2_name'])) {
            $options['cardlimit2_name'] = $builder->getName().'2';
        }
        $builder->setAttribute('cardlimit1_name', $options['cardlimit1_name']);
        $builder->setAttribute('cardlimit2_name', $options['cardlimit2_name']);

        $builder->add('cardlimit1', 'choice', 
                    array_merge_recursive($options['options'], 
                        array(
                                'label' => '有効期限',
                                'choices' => explode(',',$this->config['cardlimit_mon']),
                            )                
                        )
                    );

        $curryear = date("Y");
        for($i=0;$i<15;$i++){
            $curryeararr[] =$curryear;
            $curryear++;
        }
        $builder->add('cardlimit2', 'choice', 
                    array_merge_recursive($options['options'], 
                        array(
                                'label' => '有効期限',
                                'choices' => $curryeararr,
                            )                
                        )
                    );






    }
    // /**
    //  * {@inheritdoc}
    //  */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
         $builder = $form->getConfig();
         $view->vars['cardlimit1_name'] = $builder->getAttribute('cardlimit1_name');
         $view->vars['cardlimit2_name'] = $builder->getAttribute('cardlimit2_name');
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
            'cardlimit1_options' => array(
            ),
            'cardlimit2_options' => array(
            ),
            'cardlimit1_name' => '',
            'cardlimit2_name' => '',
            'error_bubbling' => false,
            'inherit_data' => true,
            'trim' => true,
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'cardlimit';
    }
}
