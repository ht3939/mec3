<?php

namespace Plugin\SimpleStateChange\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UpdateStatusType extends AbstractType
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('Status', 'disp', array(
            'constraints' => array(
                new Assert\NotBlank(),
            ),
        ))
        ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_update_status';
    }
}