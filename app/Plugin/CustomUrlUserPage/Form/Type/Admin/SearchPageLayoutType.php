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


namespace Plugin\CustomUrlUserPage\Form\Type\Admin;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchPageLayoutType extends AbstractType
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
/*
        $builder
            ->add('id', 'text', array(
                'label' => 'ページID',
                'required' => false,
            ))
            ->add('pagename', 'text', array(
                'label' => 'ページ名',
                'required' => false,
            ))
            ->add('url', 'text', array(
                'label' => 'ユーザー定義URL',
                'required' => false,
            ))

            ->add('filename', 'text', array(
                'label' => 'ページファイル名',
                'required' => false,
            ))
        ;
*/
    }


    /**
     * {@inheritdoc}
     */
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            //'compound'=> true,
            //'mapped'=>false,
            //'class' => 'Eccube\Entity\PageLayout',
            'class' => 'Plugin\CustomUrlUserPage\Entity\PageLayout',
            'multiple'=> false,
            'expanded' => false,
            'required' => false,
            'empty_value' => '-',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('m')
                    ->Where('m.edit_flg=0')
                    ->orderBy('m.id', 'ASC');
            },
        ));
    
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_search_pagelayout';
    }
    /**
     * {@inheritdoc}
     */
    
    public function getParent()
    {
        return 'entity';
    }
    
}
