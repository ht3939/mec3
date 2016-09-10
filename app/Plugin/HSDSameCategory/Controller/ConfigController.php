<?php

namespace Plugin\HSDSameCategory\Controller;
use Eccube\Application;
use Plugin\HSDSameCategory\Entity\HSDSameCategorySetting;
use Symfony\Component\HttpFoundation\Request;

class ConfigController
{
    /**
     * HSDSameCategory設定画面
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {

        $gfe = $app['hsd_same_category_setting.repository.hsd_same_category_setting']->find(1);
        if ( !$gfe ) {
            $gfe = new HSDSameCategorySetting();
        }
        $form = $app['form.factory']->createBuilder('hsd_same_category_setting_config', $gfe)->getForm();

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            //if ($form->isValid()) {
                $Relationproduct = $form->getData();

                // IDは1固定
                $Relationproduct->setId(1);

                $app['orm.em']->persist($Relationproduct);
                $app['orm.em']->flush();
                $app->addSuccess('admin.samecategorysetting.save.complete', 'admin');
            //}

        }

        return $app->render('HSDSameCategory/Resource/template/admin/config.twig', array(
            'form' => $form->createView()
        ));

    }
}

?>

