<?php

namespace Plugin\MatchProduct\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MatchProductSettingConfigType extends AbstractType
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
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Plugin\MatchProduct\Entity\MatchProductSetting',
        ));
    }

    public function getName()
    {
        return 'match_product_setting_config';
    }
}

?>