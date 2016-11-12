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
use Symfony\Component\HttpFoundation\Request;

class RedirectController
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

        $redirect = null;

        $redirectto = $app['eccube.plugin.shoppingex.service.shoppingex']->getRedirectTo();
//dump($redirectto);die();
        if(isset( $redirectto[$request->getPathInfo()])){
            $redirect = $redirectto[$request->getPathInfo()];
            if($request->getQueryString()){
                if(explode('?',$request)>1){
                $redirect .= '&'.$request->getQueryString();

                }else{

                $redirect .= '?'.$request->getQueryString();
                }
            }
// dump($request);
// dump($request->getRequestUri());
// dump($request->getUri());
// dump($redirect);
//die();
        }

        if($redirect){
            return $app->redirect($redirect,301);

        }
        return $app->redirect($app->url('homepage'));

        //return $app->redirect(url('home_page'));
    }

}
