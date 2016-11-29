<?php

namespace Plugin\TagEx\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UpdateTagType extends AbstractType
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
        ->add('Tag', 'tag', array(
            'required' => false,
            'multiple' => true,
            'expanded' => true,
            'mapped' => false,
        ))
        ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_update_tag';
    }
}