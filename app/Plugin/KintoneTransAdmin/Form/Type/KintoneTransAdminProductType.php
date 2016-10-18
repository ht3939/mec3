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

namespace Plugin\KintoneTransAdmin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvents;
use Eccube\Form\DataTransformer;
use Symfony\Component\Form\CallbackTransformer;
class KintoneTransAdminProductType extends AbstractType
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
                'label' => '連携ID',
                'required' => false,
                'attr' => array('readonly' => 'readonly'),
            ))
            ->add('tagtype', 'choice', array(
                'label' => '連携先',
                'choices'  => array(
                    'webreg' => 'webreg',
                    'kintone' => 'kintone',
                ),
                // *this line is important*
                'choices_as_values' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))              
            ->add('enable_flg', 'checkbox', array(
                'label' => '有効・無効',
                'required' => true,
                'trim' => true,
                'value' => 1,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('conditions', 'textarea', array(
                'label' => 'マッピング（Json',
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
            ));


        $builder->get('enable_flg')
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


        $builder
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($app) {
                $form = $event->getForm();
                $data = $form->getData();


                // $KintoneTransAdminCondition = $app['eccube.plugin.kintonetransadmin.repository.kintonetransadmin_product']->findBy(array('abtestidentity' => $data['abtestidentity']));

                // if ($KintoneTransAdminCondition) {
                //     //check existing Product, except itself
                //     if (($KintoneTransAdminCondition[0]->getId() != $data['id'])) {
                //         $form['abtestidentity']->addError(new FormError('既に同じ識別子が追加されています。'));
                //     }
                // }

            });

        $builder->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Plugin\KintoneTransAdmin\Entity\KintoneTransAdminProduct',
        ));
    }


    /**
     *
     * @ERROR!!!
     *
     */
    public function getName()
    {
        return 'admin_kintonetransadmin';
    }
}
