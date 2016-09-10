<?php

/*
 * This file is part of the SEO3
 *
 * Copyright (C) 2016 Nobuhiko Kimoto
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\SEO3\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class ConfigController
{

    /**
     * SEO3用設定画面
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        $db = __DIR__ . '/../Resource/db.txt';
        // DB使うのがめんどくさいのでファイルで制御
        $array = @unserialize(@file_get_contents($db));
        $defaults = array(
            'title' => $array['title'],
        );

        $form = $app['form.factory']->createBuilder('seo3_config', $defaults)->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();
                if (file_put_contents($db, serialize($data)) === FALSE) {
                    throw new HttpException();
                }
            }
        }

        return $app->render('SEO3/Resource/template/admin/config.twig', array(
            'form' => $form->createView(),
        ));
    }

}
