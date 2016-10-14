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

namespace Plugin\ABTestCfg\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvents;
use Eccube\Form\DataTransformer;

class ABTestCfgProductType extends AbstractType
{

    private $app;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
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
        $app = $this->app;

        $builder
            ->add('id', 'text', array(
                'label' => 'FDルート管理ID',
                'required' => false,
                'attr' => array('readonly' => 'readonly'),
            ))
            ->add('abtestidentity', 'text', array(
                'label' => 'ABテスト識別子',
                'required' => true,
                'trim' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('enable_flg', 'integer', array(
                'label' => '有効・無効',
                'required' => true,
                'trim' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('headtags', 'textarea', array(
                'label' => 'GTMタグ',
                'required' => false,
                'trim' => true,
            ))
            ->add('tagdevice', 'text', array(
                'label' => '対象デバイス',
                'required' => true,
                'trim' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('conditions', 'textarea', array(
                'label' => 'ルートパラメータ条件',
                'required' => true,
                'trim' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('tagurl', 'text', array(
                'label' => '対象URL',
                'required' => true,
                'trim' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('abrule', 'text', array(
                'label' => 'ab判定方法(rule,url)',
                'required' => true,
                'trim' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('aburl', 'text', array(
                'label' => 'ab判定url',
                'required' => false,
                'trim' => true,
                
            ))
            ->add('organic_flg', 'integer', array(
                'label' => 'オーガニック対象',
                'required' => true,
                'trim' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ));



        $builder
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($app) {
                $form = $event->getForm();
                $data = $form->getData();


                $ABTestCfgCondition = $app['eccube.plugin.abtestcfg.repository.abtestcfg_product']->findBy(array('abtestidentity' => $data['abtestidentity']));

                if ($ABTestCfgCondition) {
                    //check existing Product, except itself
                    if (($ABTestCfgCondition[0]->getId() != $data['id'])) {
                        $form['abtestidentity']->addError(new FormError('既に同じ識別子が追加されています。'));
                    }
                }

            });

        $builder->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Plugin\ABTestCfg\Entity\ABTestCfgProduct',
        ));
    }


    /**
     *
     * @ERROR!!!
     *
     */
    public function getName()
    {
        return 'admin_abtestcfg';
    }
}
