<?php

namespace Plugin\HSDRelatedProduct\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class HSDRelatedProductSettingConfigType extends AbstractType
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('max_num', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('max_row_num', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('title', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('show_price', 'choice', array(
                'choices' => array('show_price' => '価格を表示する', 'not_show_price' => '価格を表示しない'),
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('show_type', 'choice', array(
                'choices' => array('normal' => 'スライダーなし', 'per3' => 'スライダーあり 3個表示', 'per4' => 'スライダーあり 4個表示', 'per5' => 'スライダーあり 5個表示', 'flip' => 'スライダー フリップ', 'cube' => 'スライダー 3Dキューブ', 'coverflow' => 'スライダー カバーフロー'),
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('pagination', 'choice', array(
                'choices' => array('true' => 'あり', 'false' => 'なし'),
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('navbuttons', 'choice', array(
                'choices' => array('true' => 'あり', 'false' => 'なし'),
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('showloop', 'choice', array(
                'choices' => array('true' => 'あり', 'false' => 'なし'),
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Plugin\HSDRelatedProduct\Entity\HSDRelatedProductSetting',
        ));
    }

    public function getName()
    {
        return 'hsd_related_product_setting_config';
    }
}

?>