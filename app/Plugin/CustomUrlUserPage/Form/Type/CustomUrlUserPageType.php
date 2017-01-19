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

namespace Plugin\CustomUrlUserPage\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvents;
use Eccube\Form\DataTransformer;
use Symfony\Component\Form\CallbackTransformer;

class CustomUrlUserPageType extends AbstractType
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
            ->add('customurl', 'text', array(
                'label' => 'カスタムURL',
                'required' => true,
            ))
            ->add('userpage', 'text', array(
                'label' => 'ユーザー定義ページ',
                'required' => false,
            ))
            ->add('bindname', 'text', array(
                'label' => 'バインド名',
                'required' => false,
            ))
            ->add('pagethumbnail', 'text', array(
                'label' => 'ページサムネイル',
                'required' => false,
            ))
            ->add('pagecategorykey', 'text', array(
                'label' => 'カテゴリキー',
                'required' => false,
            ))
            ->add('index_flg', 'checkbox', array(
                'label' => '一覧ページフラグ',
                'required' => false,
            ))
            
            ->add('pagelayout', 'admin_search_pagelayout', array(
                 'label' => 'ユーザー定義ページ',
                 'required' => false,
            ))
            
            ->add('pageinfo', 'textarea', array(
                'label' => '一覧ページコンテンツ',
                'required' => false,
                'trim' => true,
                // 'constraints' => array(
                //     new Assert\NotBlank(),
                // ),
            ));

        $builder->get('index_flg')
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

        // $builder
        //     ->add($builder->create('PageLayout', 'hidden')
        //         ->addModelTransformer(new DataTransformer\EntityToIdTransformer(
        //             $this->app['orm.em'],
        //             '\Eccube\Entity\PageLayout'
        //         )));

        $builder
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($app) {
                $form = $event->getForm();
                $data = $form->getData();

                $PageLayout = $data['pagelayout'];

                if (empty($PageLayout)) {
                    $form['pageinfo']->addError(new FormError('商品を追加してください。'));
                } else {
                    $CustomUrlUserPage = $app['eccube.plugin.customurluserpage.repository.customurluserpage']->findBy(array('PageLayout' => $PageLayout));

                    if ($CustomUrlUserPage) {
                        //check existing PageLayout, except itself
                        if (($CustomUrlUserPage[0]->getId() != $data['id'])) {
                            $form['pageinfo']->addError(new FormError('既に商品が追加されています。'));
                        }
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
            'data_class' => 'Plugin\CustomUrlUserPage\Entity\CustomUrlUserPage',
        ));
    }


    /**
     *
     * @ERROR!!!
     *
     */
    public function getName()
    {
        return 'admin_customurluserpage';
    }
}
