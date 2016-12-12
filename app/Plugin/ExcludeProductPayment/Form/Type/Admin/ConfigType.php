<?php
/*
 * Copyright(c) 2015 SystemFriend Inc. All rights reserved.
 * http://ec-cube.systemfriend.co.jp/
 */

namespace Plugin\ExcludeProductPayment\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;

class ConfigType extends AbstractType
{
    private $app;
    private $const;
    private $subData;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;

        /* @var $Setting \Plugin\ExcludeProductPayment\Service\ConfigService */
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
        /* @var $Setting \Plugin\ExcludeProductPayment\Service\ConfigService */
        $Setting = $this->app['eccube.plugin.service.cpr.config'];

        $builder
            ->add('redirect_select', 'choice', array(
                'label' => '表示するページ',
                'required' => true,
                'choices' => $Setting->getRedirectPages(),
                'expanded' => true,
                'multiple' => false,
                'constraints' => array(
                    new Assert\NotBlank(array('message' => '表示するページが選択されていません。')),
                ),
                'attr' => array(
                    'onclick' => 'show()',
                )
            ))
            ->add('redirect_url', 'text', array(
                'label' => 'URL指定',
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
            ->add('guide_flg', 'choice', array(
                'label' => '案内ページの表示',
                'choices' => array(1 => '表示する', 0 => '表示しない'),
                'expanded' => true,
                'multiple' => false,
                'constraints' => array(
                    new Assert\NotBlank(array('message' => '案内ページの表示が選択されていません。')),
                ),
            ))
            ->add('wait_time', 'number', array(
                'label' => '案内ページの表示時間(秒)',
                'required' => false,
                'constraints' => array(
                    new Assert\Regex(array('pattern' => '/\A\d+\z/', 'message' => '半角数字を入力してください。')),
                    new Assert\GreaterThanOrEqual(array('value' => 1, 'message' => '1以上の値を入力してください。')),
                    new Assert\Length(array(
                        'max' => 9,
                    )),
                ),
            ))
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
                $form = $event->getForm();
                $data = $form->getData();

                if (empty($data['redirect_url']) && $data['redirect_select'] == $this->const['redirect_url']) {
                    $form['redirect_url']->addError(new FormError('URLを指定してください。'));
                }
                if (empty($data['wait_time']) && $data['guide_flg'] == 1) {
                    $form['wait_time']->addError(new FormError('案内ページの表示時間を入力してください。'));
                }
            })
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }



    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'cpr_config';
    }
}
