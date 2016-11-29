<?php

/*
 * This file is part of the CRM3
 *
 * Copyright (C) 2016 Nobuhiko Kimoto
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\CRM3\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class CRM3Controller
{

    /**
     * CRM3画面
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        $pagination = null;

        $pagination = $app['eccube.plugin.crm3.repository.contact']->findList();

        return $app->render('CRM3/Resource/template/admin/index.twig', array(
            'pagination' => $pagination,
            'totalItemCount' => count($pagination)
        ));
    }

}
