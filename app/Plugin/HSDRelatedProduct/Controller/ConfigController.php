<?php

namespace Plugin\HSDRelatedProduct\Controller;
use Eccube\Application;
use Plugin\HSDRelatedProduct\Entity\HSDRelatedProductSetting;
use Symfony\Component\HttpFoundation\Request;

class ConfigController
{
    /**
     * HSDRelatedProduct設定画面
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {

        $gfe = $app['hsd_related_product_setting.repository.hsd_related_product_setting']->find(1);
        if ( !$gfe ) {
            $gfe = new HSDRelatedProductSetting();
        }
        $form = $app['form.factory']->createBuilder('hsd_related_product_setting_config', $gfe)->getForm();

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            //if ($form->isValid()) {
                $Relationproduct = $form->getData();

                // IDは1固定
            $Relationproduct->setId(1);

                $app['orm.em']->persist($Relationproduct);
                $app['orm.em']->flush();
                $app->addSuccess('admin.relatedproductsetting.save.complete', 'admin');
            //}

        }

        return $app->render('HSDRelatedProduct/Resource/template/admin/config.twig', array(
            'form' => $form->createView()
        ));

    }
}

?>

