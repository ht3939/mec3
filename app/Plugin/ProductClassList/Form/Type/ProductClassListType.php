<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2015 Takashi Otaki All Rights Reserved.
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\ProductClassList\Form\Type;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\Extension\Core\Type;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Validator\Constraints as Assert;
use \Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class ProductClassListType extends AbstractType
{

  public $config;
  public $security;
  public $customerFavoriteProductRepository;
  public $Product = null;

  public function __construct(
      $config,
      \Symfony\Component\Security\Core\SecurityContext $security,
      \Eccube\Repository\CustomerFavoriteProductRepository $customerFavoriteProductRepository
  ) {
      $this->config = $config;
      $this->security = $security;
      $this->customerFavoriteProductRepository = $customerFavoriteProductRepository;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
      /* @var $Product \Eccube\Entity\Product */
      $Product = $options['product'];
      $this->Product = $Product;
      $ProductClasses = $Product->getProductClasses();

      if ($Product && $Product->getProductClasses()) {
        if ($Product->getClassName1()) {

            $classCategoryIdArray = array();
            $classCategoryFullNameArray = array();

          foreach ($ProductClasses as $ProductClassesList) {
              array_push($classCategoryIdArray, $ProductClassesList->getId());

              if ($Product->getClassName2()) {

                $classCategoryName1 = $ProductClassesList->getClassCategory1()->getName();
                $classCategoryName2 = $ProductClassesList->getClassCategory2() ? $ProductClassesList->getClassCategory2()->getName() . ($ProductClassesList->getStockFind() ? '' : ' (品切れ中)') : '';
                $classCategoryFullName = $classCategoryName1 . ' ' . $classCategoryName2 . ' ¥' . $ProductClassesList->getPrice02IncTax();
                array_push($classCategoryFullNameArray, $classCategoryFullName);
              } else {
                $classCategoryName1 = $ProductClassesList->getClassCategory1() ? $ProductClassesList->getClassCategory1()->getName() . ($ProductClassesList->getStockFind() ? '' : ' (品切れ中)') : '';
                $classCategoryFullName = $classCategoryName1 . ' ¥' . $ProductClassesList->getPrice02IncTax();
                array_push($classCategoryFullNameArray, $classCategoryFullName);
              }
          }

          $classCategoryToForm = array_combine($classCategoryIdArray, $classCategoryFullNameArray);

        }
      }

      $builder
          ->add('mode', 'hidden', array(
              'data' => 'product_class_list',
          ))
          ->add('product_id', 'hidden', array(
              'data' => $Product->getId(),
              'constraints' => array(
                  new Assert\NotBlank(),
                  new Assert\Regex(array('pattern' => '/^\d+$/')),
              ),
          ))
          ->add('product_class_id', 'hidden', array(
              'data' => count($ProductClasses) === 1 ? $ProductClasses[0]->getId() : '',
              'constraints' => array(
                  new Assert\Regex(array('pattern' => '/^\d+$/')),
              ),
          ))
        ;


      if ($Product->getStockFind()) {
          $builder
              ->add('quantity', 'integer', array(
                  'data' => 1,
                  'attr' => array(
                      'min' => 1,
                      'maxlength' => $this->config['int_len'],
                  ),
                  'constraints' => array(
                      new Assert\NotBlank(),
                      new Assert\GreaterThanOrEqual(array(
                          'value' => 1,
                      )),
                      new Assert\Regex(array('pattern' => '/^\d+$/')),
                  ),
              ))
          ;

          if ($Product && $Product->getProductClasses()) {
              if ($Product->getClassName1()) {
                  $builder->add('product_class', 'choice', array(
                    'choices'   => $classCategoryToForm,
                    'multiple'  => false,
                    'expanded' => true,
                    'data' => $ProductClasses[0]->getId(),
                ));
              }
            }

      }
  }

  /**
   * {@inheritdoc}
   */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
      $resolver->setRequired('product');
      $resolver->setDefaults(array(
          'id_add_product_id' => true,
      ));
  }

  /*
   * {@inheritdoc}
   */
  public function finishView(FormView $view, FormInterface $form, array $options)
  {
      if ($options['id_add_product_id']) {
          foreach ($view->vars['form']->children as $child) {
              $child->vars['id'] .= $options['product']->getId();
          }
      }

      if ($view->vars['form']->children['mode']->vars['value'] === 'product_class_list') {
          $view->vars['form']->children['mode']->vars['value'] = '';
      }
  }

  /**
   * {@inheritdoc}
   */
  public function getName()
  {
      return 'product_class_list';
  }

}
