<?php
/*
 * Copyright(c) 2016 SYSTEM_KD
 */

namespace Plugin\DirectCartIn\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Eccube\Common\Constant;

class ProductClassViewController
{

    /**
     * 商品詳細情報
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(Application $app, Request $request)
    {

        $this->app = $app;
        $const = $app['config']['DirectCartIn']['const'];

        if ($request->isXmlHttpRequest()) {

            // 商品情報取得
            $id = $request->get('id');

            /* @var $Product \Eccube\Entity\Product */
            $Product = $app['eccube.repository.product']->get($id);

            /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
            $builder = $app['form.factory']->createNamedBuilder('', 'add_cart', null, array(
                'product' => $Product,
                'id_add_product_id' => false,
            ));

            $form = $builder->getForm();

            if (!$request->getSession()->has('_security_admin') && $Product->getStatus()->getId() !== 1) {
                // 取得不可
                return;
            }

            if (count($Product->getProductClasses()) < 1) {
                // 取得不可
                return;
            }

            // View制御
            $tag_flg = false;
            if(Constant::VERSION != "3.0.9") {
                // 3.0.9以降
                $tag_flg = true;
            }

            // 商品価格制御
            $twig = "";
            if(Constant::VERSION == "3.0.9" || Constant::VERSION == "3.0.10") {
                $twig = $const['RENDER_TEMPLATE_PATH']."Product/product_class_view_3010.twig";
            } else {
                $twig = $const['RENDER_TEMPLATE_PATH']."Product/product_class_view.twig";
            }

            return $app->render($twig, array(
                'Product' => $Product,
                'form' => $form->createView(),
                'tag_flg' => $tag_flg,
            ));
        }
    }
}