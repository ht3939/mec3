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
        //doctrineのバグ回避用
        $queryparams = $request->query->all();
        if(isset($queryparams['sort'])){
            unset($queryparams['sort']);
        }
        $redirectto = $app['eccube.plugin.shoppingex.service.shoppingex']->getRedirectTo();
        if(isset( $redirectto[$request->getPathInfo()])){
            $redirect = $redirectto[$request->getPathInfo()];
            $query = "";
            $querys = array();
            foreach($queryparams as $k=>$v){
                if(is_array($v)){
                    foreach ($v as $value) {
                        $querys[]= $k.'[]='.$value;
                        
                    }

                }else{
                    $querys[]= $k.'='.$v;

                }

            }
            $query = implode('&',$querys);
            if(count($queryparams)>0){
                if(count(explode('?',$redirect))>1){
                $redirect .= '&'.urldecode(($query));

                }else{

                $redirect .= '?'.urldecode(($query));
                }
            }
        }

        if($redirect){
            return $app->redirect($redirect,301);

        }
        return $app->redirect($app->url('homepage'));

        //return $app->redirect(url('home_page'));
    }

}
