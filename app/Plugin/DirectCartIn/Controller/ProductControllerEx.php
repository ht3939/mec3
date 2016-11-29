<?php
/*
 * Copyright(c) 2016 SYSTEM_KD
 */

namespace Plugin\DirectCartIn\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Eccube\Controller\ProductController;

class ProductControllerEx extends ProductController
{

    public function detail(Application $app, Request $request, $id)
    {
        // 3.0.8 以前のフックポイントの影響を受けないよう継承
        return  parent::detail($app, $request, $id);
    }
}