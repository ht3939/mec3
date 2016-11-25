<?php
/*
 * Copyright(c) 2015 SystemFriend Inc. All rights reserved.
 * http://ec-cube.systemfriend.co.jp/
 */

namespace Plugin\CloseProductRedirect\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductRedirectType extends AbstractType
{
    private $app;
    private $const;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;

        /* @var $Setting \Plugin\CloseProductRedirect\Service\ConfigService */
        $Setting = $this->app['eccube.plugin.service.cpr.config'];
        $this->const = $Setting->getConst();
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
        /* @var $Setting \Plugin\CloseProductRedirect\Service\ConfigService */
        $Setting = $this->app['eccube.plugin.service.cpr.config'];

        $builder
            ->add('redirect_select', 'choice', array(
                'label' => 'リダイレクト先',
                'choices' => $Setting->getRedirectPages(false),
                'expanded' => true,
                'multiple' => false,
                'constraints' => array(
                    new Assert\NotBlank(array('message' => 'リダイレクト先が選択されていません。')),
                ),
                'attr' => array(
                    'onclick' => 'show()',
                )
            ))
            ->add('redirect_url', 'text', array(
                'label' => 'URL',
                'required' => false,
                'constraints' => array(
//                    new Assert\Url(),
                    new Assert\Regex(array('pattern' => '/^[[:graph:][:space:]]+$/i', 'message' => '半角英数記号を入力してください。')),
                    new Assert\Regex(array('pattern' => '@^https?://+($|[a-zA-Z0-9_~=:&\?\.\/-])+$@i', 'message' => 'URL形式を入力してください')),
                    new Assert\Length(array(
                        'max' => 1000,
                    )),
                ),
            ))
            ->add('redirect_product_id', 'number', array(
                'label' => '商品ID',
                'required' => false,
                'constraints' => array(
                    new Assert\Regex(array('pattern' => '/\A\d+\z/', 'message' => '半角数字を入力してください。')),
                    new Assert\Length(array(
                        'max' => 9,
                    )),
                    new Assert\GreaterThanOrEqual(array('value' => 1)),
                ),
            ))
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
                $form = $event->getForm();
                $data = $form->getData();

                if (empty($data['redirect_url']) && $data['redirect_select'] == $this->const['redirect_url']) {
                    $form['redirect_url']->addError(new FormError('URLを入力してください。'));
                }
                if (empty($data['redirect_product_id']) && $data['redirect_select'] == $this->const['redirect_id']) {
                    $form['redirect_product_id']->addError(new FormError('商品IDを入力してください。'));
                }
            })
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Plugin\CloseProductRedirect\Entity\CprProductRedirect',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_product_redirect';
    }
}
