<?php

/*
 * This file is part of the SEO3
 *
 * Copyright (C) 2016 Nobuhiko Kimoto
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ShoppingEx\Controller;

use Eccube\Application;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\PageLayout;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AboutSimController extends \Eccube\Controller\UserDataController
{

    // public function index(Application $app)
    // {

    //     //return $app->render($app['config']['user_data_realdir'].'/about_sim.twig');
    //         return $app->render('about_sim.twig');
    // return $app->render('about_sim.twig');
    // }
    public function index(Application $app, Request $request, $route='about-sim')
    {
        return parent::index($app,$request,$route);
    }
}
