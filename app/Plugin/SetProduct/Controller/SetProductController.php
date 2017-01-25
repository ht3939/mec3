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

namespace Plugin\SetProduct\Controller;

use Plugin\SetProduct\Form\Type\SetProductType;
use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception as HttpException;

class SetProductController
{
    private $main_title;
    private $sub_title;

    public function __construct()
    {
    }

    public function index(Application $app, Request $request, $id)
    {
    	$repos = $app['eccube.plugin.setproduct.repository.setproduct'];

		$TargetSetProduct = new \Plugin\SetProduct\Entity\SetProduct();

        if ($id) {
            $TargetSetProduct = $repos->find($id);
            if (!$TargetSetProduct) {
                throw new NotFoundHttpException();
            }
        }

        $form = $app['form.factory']
            ->createBuilder('admin_setproduct', $TargetSetProduct)
            ->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $status = $repos->save($TargetSetProduct);

                if ($status) {
                    $app->addSuccess('admin.setproduct.save.complete', 'admin');
                    return $app->redirect($app->url('admin_setproduct'));
                } else {
                    $app->addError('admin.setproduct.save.error', 'admin');
                }
            }
        }
    	
        $SetProducts = $app['eccube.plugin.setproduct.repository.setproduct']->findAll();

        return $app->render('SetProduct/View/admin/setproduct.twig', array(
        	'form'   		=> $form->createView(),
            'SetProducts' 		=> $SetProducts,
            'TargetSetProduct' 	=> $TargetSetProduct,
        ));
    }

    public function delete(Application $app, Request $request, $id)
    {
    	$repos = $app['eccube.plugin.setproduct.repository.setproduct'];

        $TargetSetProduct = $repos->find($id);
        
        if (!$TargetSetProduct) {
            throw new NotFoundHttpException();
        }

        $form = $app['form.factory']
            ->createNamedBuilder('admin_setproduct', 'form', null, array(
                'allow_extra_fields' => true,
            ))
            ->getForm();

        $status = false;
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $repos->delete($TargetSetProduct);
            }
        }

        if ($status === true) {
            $app->addSuccess('admin.setproduct.delete.complete', 'admin');
        } else {
            $app->addError('admin.setproduct.delete.error', 'admin');
        }

        return $app->redirect($app->url('admin_setproduct'));
    }

    public function up(Application $app, Request $request, $id)
    {
    	$repos = $app['eccube.plugin.setproduct.repository.setproduct'];
    	
        $TargetSetProduct = $repos->find($id);
        if (!$TargetSetProduct) {
            throw new NotFoundHttpException();
        }

        $form = $app['form.factory']
            ->createNamedBuilder('admin_setproduct', 'form', null, array(
                'allow_extra_fields' => true,
            ))
            ->getForm();

        $status = false;
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $repos->up($TargetSetProduct);
            }
        }

        if ($status === true) {
            $app->addSuccess('admin.setproduct.down.complete', 'admin');
        } else {
            $app->addError('admin.setproduct.down.error', 'admin');
        }

        return $app->redirect($app->url('admin_setproduct'));
    }

    public function down(Application $app, Request $request, $id)
    {
    	$repos = $app['eccube.plugin.setproduct.repository.setproduct'];
    	
        $TargetSetProduct = $repos->find($id);
        if (!$TargetSetProduct) {
            throw new NotFoundHttpException();
        }

        $form = $app['form.factory']
            ->createNamedBuilder('admin_setproduct', 'form', null, array(
                'allow_extra_fields' => true,
            ))
            ->getForm();

        $status = false;
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $status = $repos->down($TargetSetProduct);
            }
        }

        if ($status === true) {
            $app->addSuccess('admin.setproduct.down.complete', 'admin');
        } else {
            $app->addError('admin.setproduct.down.error', 'admin');
        }

        return $app->redirect($app->url('admin_setproduct'));
    }

}
