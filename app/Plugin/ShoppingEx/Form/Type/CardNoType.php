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

class CardNoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function __construct($config = array(
        'card_len' => 4, 'card_len_min' => 2,
        'cardtype' => 'Visa,JCB,AmericanExpress,Master,DinersClub',
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
        $options['cardno1_options']['required'] = $options['required'];
        $options['cardno2_options']['required'] = $options['required'];
        $options['cardno3_options']['required'] = $options['required'];
        $options['cardno4_options']['required'] = $options['required'];
        $options['holder_options']['required'] = $options['required'];
        $options['cardtype_options']['required'] = $options['required'];
        $options['cardlimitmon_options']['required'] = $options['required'];
        $options['cardlimityear_options']['required'] = $options['required'];
        $options['cardsec_options']['required'] = $options['required'];

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
        if (empty($options['cardno1_name'])) {
            $options['cardno1_name'] = $builder->getName().'1';
        }
        if (empty($options['cardno2_name'])) {
            $options['cardno2_name'] = $builder->getName().'2';
        }
        if (empty($options['cardno3_name'])) {
            $options['cardno3_name'] = $builder->getName().'3';
        }
        if (empty($options['cardno4_name'])) {
            $options['cardno4_name'] = $builder->getName().'4';
        }
        if (empty($options['holder_name'])) {
            $options['holder_name'] = 'holder';
        }
        if (empty($options['cardtype_name'])) {
            $options['cardtype_name'] = 'cardtype';
        }
        if (empty($options['cardlimitmon_name'])) {
            $options['cardlimitmon_name'] = 'cardlimitmon';
        }
        if (empty($options['cardlimityear_name'])) {
            $options['cardlimityear_name'] = 'cardlimityear';
        }
        if (empty($options['cardsec_name'])) {
            $options['cardsec_name'] = 'cardsec';
        }

        $cardtypearr = explode(',',$this->config['cardtype']);
        $tmpmon = explode(',',$this->config['cardlimit_mon']);
        foreach( $tmpmon as $mon){
            $currmonarr[$mon] = $mon;
        }
        $curryear = date("Y");
        for($i=0;$i<15;$i++){
            $curryeararr[$curryear] =$curryear;
            $curryear++;
        }

        // 全角英数を事前に半角にする
        $builder->addEventSubscriber(new \Eccube\EventListener\ConvertKanaListener());
        $builder
            ->add($options['cardno1_name'], 'text', array_merge_recursive($options['options'], 
                        array(
                                'attr' => array(
                                     'placeholder' => '例) 1234000012340000',
                                ),
                                'label' => 'カード番号'
                           )                
                )
            )
            //->add($options['cardno2_name'], 'text', array_merge_recursive($options['options'], $options['cardno2_options']))
            //->add($options['cardno3_name'], 'text', array_merge_recursive($options['options'], $options['cardno3_options']))
            //->add($options['cardno4_name'], 'text', array_merge_recursive($options['options'], $options['cardno4_options']))
            ->add($options['holder_name'], 'text', array_merge_recursive($options['options'], 
                        array(
                                'attr' => array(
                                     'placeholder' => '例） YAMADA TARO',
                                ),
                                'label' => 'カード所有者'
                           )                
                )
            )
            ->add($options['cardtype_name'], 'choice', 
                    array_merge_recursive($options['options'], 
                        array(
                                'label' => 'カード種別',
                                'choices' => $cardtypearr,
                            )                
                        )
                    )
            ->add($options['cardlimitmon_name'], 'choice',
                     array_merge_recursive($options['options'],
                         array(
                                'choices' => $currmonarr,
                                'label' => '有効期限（月／年）'
                             )
                         )
                     )

            ->add($options['cardlimityear_name'], 'choice',
                     array_merge_recursive($options['options'],
                         array(
                                'choices' => $curryeararr,
                                'label' => ' '
                             )
                         )
                     )
            ->add($options['cardsec_name'], 'text', 
                    array_merge_recursive($options['options'], 
                        array(
                                'attr' => array(
                                     'placeholder' => '例） 123',
                                ),
                                'label' => 'セキュリティコード'
                            )                
                        )
                    )
        ;

        $builder->setAttribute('cardno1_name', $options['cardno1_name']);
        //$builder->setAttribute('cardno2_name', $options['cardno2_name']);
        //$builder->setAttribute('cardno3_name', $options['cardno3_name']);
        //$builder->setAttribute('cardno4_name', $options['cardno4_name']);
        $builder->setAttribute('holder_name', $options['holder_name']);
        $builder->setAttribute('cardtype_name', $options['cardtype_name']);
        $builder->setAttribute('cardlimitmon_name', $options['cardlimitmon_name']);
        $builder->setAttribute('cardlimityear_name', $options['cardlimityear_name']);
        $builder->setAttribute('cardsec_name', $options['cardsec_name']);

        // todo 変
        
        $builder->addEventListener(FormEvents::POST_BIND, function ($event) use ($builder) {
            $form = $event->getForm();
            $count = 0;
            if ($form[$builder->getName().'1']->getData() != '') {
                $count++;
            }
            /*
            if ($form[$builder->getName().'2']->getData() != '') {
                $count++;
            }
            if ($form[$builder->getName().'3']->getData() != '') {
                $count++;
            }
            if ($form[$builder->getName().'4']->getData() != '') {
                $count++;
            }
            */
            if ($count != 0 && $count != 4) {
                // todo メッセージをymlに入れる
                $form[$builder->getName().'1']->addError(new FormError('全て入力してください。'));
            }
        });
        
    }
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $builder = $form->getConfig();
        $view->vars['cardno1_name'] = $builder->getAttribute('cardno1_name');
        //$view->vars['cardno2_name'] = $builder->getAttribute('cardno2_name');
        //$view->vars['cardno3_name'] = $builder->getAttribute('cardno3_name');
        //$view->vars['cardno4_name'] = $builder->getAttribute('cardno4_name');
        $view->vars['holder_name'] = $builder->getAttribute('holder_name');
        $view->vars['cardtype_name'] = $builder->getAttribute('cardtype_name');
        $view->vars['cardlimitmon_name'] = $builder->getAttribute('cardlimitmon_name');
        $view->vars['cardlimityear_name'] = $builder->getAttribute('cardlimityear_name');
        $view->vars['cardsec_name'] = $builder->getAttribute('cardsec_name');

    }

    // public function configureOptions(OptionsResolver $resolver)
    // {
    //     $resolver->setDefaults(array(
    //         'class' => 'Eccube\Entity\Payment',
    //         'property' => 'method',
    //         'empty_value' => '-',
    //         // fixme 何故かここはDESC
    //         'query_builder' => function(EntityRepository $er) {
    //             return $er->createQueryBuilder('m')
    //                 ->orderBy('m.rank', 'DESC');
    //         },
    //     ));
    // }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Plugin\ShoppingEx\Entity\ShoppingEx',
            'property' => 'method',
            'options' => array('constraints' => array()),
            'cardno1_options' => array(
                'constraints' => array(
                    new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')), //todo  messageは汎用的に出来ないものか?
                    new Assert\Length(array('max' => $this->config['cardno_len'], 'min' => 4)),
                ),
            ),
            /*
            'cardno2_options' => array(
                'constraints' => array(
                    new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')), //todo  messageは汎用的に出来ないものか?
                    new Assert\Length(array('max' => $this->config['cardno_len'], 'min' => 4)),
                ),
            ),
            'cardno3_options' => array(
                'constraints' => array(
                    new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')), //todo  messageは汎用的に出来ないものか?
                    new Assert\Length(array('max' => $this->config['cardno_len'], 'min' => 4)),
                ),
            ),
            'cardno4_options' => array(
                'constraints' => array(
                    new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')), //todo  messageは汎用的に出来ないものか?
                    new Assert\Length(array('max' => $this->config['cardno_len'], 'min' => $this->config['cardno_len_min'] )),
                ),
            ),
            */
            'holder_options' => array(
                'constraints' => array(
                    new Assert\Length(array('max' => 50, 'min' => 1)),
                ),
            ),
            'cardsec_options' => array(
                'constraints' => array(
                    new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')), //todo  messageは汎用的に出来ないものか?
                    new Assert\Length(array('max' => 4, 'min' => 3)),
                ),
            ),
            'cardno1_name' => '',
            /*
            'cardno2_name' => '',
            'cardno3_name' => '',
            'cardno4_name' => '',
            */
            'holder_name' => '',
            'cardtype_name' => '',
            'cardlimitmon_name' => '',
            'cardlimityear_name' => '',
            'cardsec_name' => '',
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
        return 'cardno';
    }
}
