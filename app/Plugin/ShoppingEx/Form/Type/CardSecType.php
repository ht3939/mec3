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

class CardSecType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function __construct($config = array('cardsec_len' => 4, 'cardsec_len_min' => 3))
    {
        $this->config = $config;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
        if (empty($options['cardsec_name'])) {
            $options['cardsec_name'] = $builder->getName().'1';
        }
        // 全角英数を事前に半角にする
        $builder->addEventSubscriber(new \Eccube\EventListener\ConvertKanaListener());
        $builder
            ->add($options['cardsec_name'], 'text', array_merge_recursive($options['options'], $options['cardsec_options']))
        ;
        $builder->setAttribute('cardsec_name', $options['cardsec_name']);

    }
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $builder = $form->getConfig();
        $view->vars['cardsec_name'] = $builder->getAttribute('cardsec_name');
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'options' => array('constraints' => array()),
            'cardsec_options' => array(
                'constraints' => array(
                    new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')), //todo  messageは汎用的に出来ないものか?
                    new Assert\Length(array('max' => $this->config['cardsec_len'], 'min' => $this->config['cardsec_len_min'])),
                ),
            ),
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
        return 'cardsec';
    }
}
