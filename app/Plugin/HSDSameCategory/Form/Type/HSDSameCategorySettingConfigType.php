<?php

namespace Plugin\HSDSameCategory\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class HSDSameCategorySettingConfigType extends AbstractType
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('max_count', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('title', 'text', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('mode', 'choice', array(
                'choices' => array('price_desc' => '価格の高い順', 'price_asc' => '価格の低い順', 'update_desc' => '更新日が新しい順', 'update_asc' => '更新日が古い順', 'random' => 'ランダム'),
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
            'data_class' => 'Plugin\HSDSameCategory\Entity\HSDSameCategorySetting',
        ));
    }

    public function getName()
    {
        return 'hsd_same_category_setting_config';
    }
}

?>