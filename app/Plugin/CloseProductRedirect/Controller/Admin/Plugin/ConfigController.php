<?php
/*
 * Copyright(c) 2015 SystemFriend Inc. All rights reserved.
 * http://ec-cube.systemfriend.co.jp/
 */

namespace Plugin\CloseProductRedirect\Controller\Admin\Plugin;

use Eccube\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;
use Eccube\Util\Cache;

class ConfigController
{

    /**
     * プラグイン設定画面
     *
     * @param Application $app
     * @param Request $request
     * @return RedirectResponse
     */
    public function edit(Application $app, Request $request)
    {
        $this->app   = $app;
        /* @var $Setting \Plugin\CloseProductRedirect\Service\ConfigService */
        $Setting     = $this->app['eccube.plugin.service.cpr.config'];
        $this->const = $Setting->getConst();

        $tpl_subtitle = $Setting->pluginName . ' の設定';
        $subData = $Setting->getSetting();

        $form = $this->app['form.factory']->createBuilder('cpr_config')->getForm();
        $form->setData($subData);

        if ('POST' === $this->app['request']->getMethod()) {
            $form->handleRequest($this->app['request']);
            if ($form->isValid()) {
                $formData = $form->getData();

                $em = $this->app['orm.em'];
                $em->getConnection()->beginTransaction();
                $Setting->registerSubData($formData);
                $this->registerPagelayout();
                $em->getConnection()->commit();

                $app->addSuccess('admin.register.complete', 'admin');
                Cache::clear($app, false);
                return $this->app->redirect($this->app['url_generator']->generate('plugin_CloseProductRedirect_config'));
            }
        }

        return $this->app['view']->render('CloseProductRedirect/Resource/template/admin/Plugin/config.twig',
            array(
                'form' => $form->createView(),
                'tpl_subtitle' => $tpl_subtitle,
                'subData' => $subData,
            ));
    }

    /**
     * ページの追加
     */
    public function registerPagelayout()
    {
        $url = "cpr_redirect_guide";
        $DeviceType = $this->app['eccube.repository.master.device_type']->find(10);
        $PageLayout = $this->app['eccube.repository.page_layout']->findOneBy(array('url' => $url));
        if (is_null($PageLayout)) {
            $PageLayout = $this->app['eccube.repository.page_layout']->newPageLayout($DeviceType);
        }

        $PageLayout->setName('ページ移動案内');
        $PageLayout->setUrl($url);
        $PageLayout->setMetaRobots('noindex');
        $PageLayout->setEditFlg('2');
        $this->app['orm.em']->persist($PageLayout);
        $this->app['orm.em']->flush();
    }

}
