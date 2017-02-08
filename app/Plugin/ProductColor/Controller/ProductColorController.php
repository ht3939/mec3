<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
* http://www.lockon.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\ProductColor\Controller;

use Plugin\ProductColor\Form\Type\ProductColorType;
use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception as HttpException;

class ProductColorController
{
    private $main_title;
    private $sub_title;

    public function __construct()
    {
    }

    public function index(Application $app, Request $request, $id)
    {
    	$repos = $app['eccube.plugin.productcolor.repository.productcolor'];

		$TargetProductColor = new \Plugin\ProductColor\Entity\ProductColor();

        if ($id) {
            $TargetProductColor = $repos->find($id);
            if (!$TargetProductColor) {
                throw new NotFoundHttpException();
            }
        }

        $form = $app['form.factory']
            ->createBuilder('admin_productcolor', $TargetProductColor)
            ->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $status = $repos->save($TargetProductColor);

                if ($status) {
                    $app->addSuccess('admin.productcolor.save.complete', 'admin');
                    return $app->redirect($app->url('admin_productcolor'));
                } else {
                    $app->addError('admin.productcolor.save.error', 'admin');
                }
            }
        }
    	
        $ProductColors = $app['eccube.plugin.productcolor.repository.productcolor']->findAll();

        return $app->render('ProductColor/View/admin/productcolor.twig', array(
        	'form'   		=> $form->createView(),
            'ProductColors' 		=> $ProductColors,
            'TargetProductColor' 	=> $TargetProductColor,
        ));
    }

    public function delete(Application $app, Request $request, $id)
    {
    	$repos = $app['eccube.plugin.productcolor.repository.productcolor'];

        $TargetProductColor = $repos->find($id);
        
        if (!$TargetProductColor) {
            throw new NotFoundHttpException();
        }

        $form = $app['form.factory']
            ->createNamedBuilder('admin_productcolor', 'form', null, array(
                'allow_extra_fields' => true,
            ))
            ->getForm();

        $status = false;
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $repos->delete($TargetProductColor);
            }
        }

        if ($status === true) {
            $app->addSuccess('admin.productcolor.delete.complete', 'admin');
        } else {
            $app->addError('admin.productcolor.delete.error', 'admin');
        }

        return $app->redirect($app->url('admin_productcolor'));
    }

    public function up(Application $app, Request $request, $id)
    {
    	$repos = $app['eccube.plugin.productcolor.repository.productcolor'];
    	
        $TargetProductColor = $repos->find($id);
        if (!$TargetProductColor) {
            throw new NotFoundHttpException();
        }

        $form = $app['form.factory']
            ->createNamedBuilder('admin_productcolor', 'form', null, array(
                'allow_extra_fields' => true,
            ))
            ->getForm();

        $status = false;
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $repos->up($TargetProductColor);
            }
        }

        if ($status === true) {
            $app->addSuccess('admin.productcolor.down.complete', 'admin');
        } else {
            $app->addError('admin.productcolor.down.error', 'admin');
        }

        return $app->redirect($app->url('admin_productcolor'));
    }

    public function down(Application $app, Request $request, $id)
    {
    	$repos = $app['eccube.plugin.productcolor.repository.productcolor'];
    	
        $TargetProductColor = $repos->find($id);
        if (!$TargetProductColor) {
            throw new NotFoundHttpException();
        }

        $form = $app['form.factory']
            ->createNamedBuilder('admin_productcolor', 'form', null, array(
                'allow_extra_fields' => true,
            ))
            ->getForm();

        $status = false;
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $repos->down($TargetProductColor);
            }
        }

        if ($status === true) {
            $app->addSuccess('admin.productcolor.down.complete', 'admin');
        } else {
            $app->addError('admin.productcolor.down.error', 'admin');
        }

        return $app->redirect($app->url('admin_productcolor'));
    }

}
