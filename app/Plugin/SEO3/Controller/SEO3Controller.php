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

class SEO3Controller
{

    /**
     * SEO3画面
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {

        // add code...

        return $app->render('SEO3/Resource/template/index.twig', array(
            // add parameter...
        ));
    }

}
