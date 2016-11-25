<?php

namespace Plugin\MatchProduct\Controller;
use Eccube\Application;
use Plugin\MatchProduct\Entity\MatchProductSetting;
use Symfony\Component\HttpFoundation\Request;

class ConfigController
{
    /**
     * MatchProduct設定画面
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {

        // $gfe = $app['maker_related_product_setting.repository.maker_related_product_setting']->find(1);
        // if ( !$gfe ) {
        //     $gfe = new MatchProductSetting();
        // }
        // $form = $app['form.factory']->createBuilder('maker_related_product_setting_config', $gfe)->getForm();

        // if ('POST' === $request->getMethod()) {

        //     $form->handleRequest($request);

        //     $Relationproduct = $form->getData();

        //     $Relationproduct->setId(1);

        //     $app['orm.em']->persist($Relationproduct);
        //     $app['orm.em']->flush();
        //     $app->addSuccess('admin.relatedproductsetting.save.complete', 'admin');

        // }

        // return $app->render('MatchProduct/Resource/template/admin/config.twig', array(
        //     'form' => $form->createView()
        // ));

    }
}

?>

