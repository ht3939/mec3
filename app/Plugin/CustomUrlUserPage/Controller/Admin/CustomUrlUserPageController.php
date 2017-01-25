<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Plugin\CustomUrlUserPage\Controller\Admin;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception as HttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;

class CustomUrlUserPageController extends AbstractController
{

    private $main_title;

    private $sub_title;

    public function __construct()
    {
    }

    /**
     * @param Application $app
     * @param Request     $request
     * @param unknown     $id
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        $pagination = null;

        $pagination = $app['eccube.plugin.customurluserpage.repository.customurluserpage']->findList();

        return $app->render('CustomUrlUserPage/Resource/template/admin/index.twig', array(
            'pagination' => $pagination,
            'totalItemCount' => count($pagination)
        ));
    }

    /**
     * @param Application $app
     * @param Request     $request
     * @param unknown     $id
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function create(Application $app, Request $request, $id)
    {
        $builder = $app['form.factory']->createBuilder('admin_customurluserpage');
        $form = $builder->getForm();


        $service = $app['eccube.plugin.customurluserpage.service.customurluserpage'];

        $Product = null;

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            $data = $form->getData();
            if ($form->isValid()) {

                $status = $service->createCustomUrlUserPage($data);

                if (!$status) {
                    $app->addError('admin.customurluserpage.notfound', 'admin');
                } else {
                    // 画像の登録
                    $add_images = $form->get('add_images')->getData();
                    $add_dlfilenames = $form->get('add_dlfilenames')->getData();
                    foreach ($add_images as $k=>$add_image) {
                        $add_dlfilename=$add_dlfilenames[$k];

                        $CustomUrlUserPageImage = new \Plugin\CustomUrlUserPage\Entity\CustomUrlUserPageImage();
                        $CustomUrlUserPageImage
                            ->setFileName($add_image)
                            ->setDlFileName($add_dlfilename)

                            ->setCustomUrlUserPage($CustomUrlUserPage)
                            ->setRank(1);
                        $CustomUrlUserPage->addCustomUrlUserPageImage($CustomUrlUserPageImage);
                        $app['orm.em']->persist($CustomUrlUserPageImage);
                        $app['orm.em']->flush();

                        // 移動
                        $file = new File($app['config']['image_temp_realdir'] . '/' . $add_image);
                        $file->move($app['config']['image_save_realdir']);
                    }

                    $ranks = $request->get('rank_images');
                    if ($ranks) {
                        foreach ($ranks as $rank) {
                            list($filename, $rank_val) = explode('//', $rank);
                            $CustomUrlUserPageImage = $app['eccube.plugin.customurluserpage.repository.customurluserpageimage']
                                ->findOneBy(array(
                                    'filename' => $filename,
                                    'CustomUrlUserPage' => $CustomUrlUserPage,
                                ));
                            $CustomUrlUserPageImage->setRank($rank_val);
                            $app['orm.em']->persist($CustomUrlUserPageImage);
                        }
                    }
                    $app['orm.em']->flush();


                    $app->addSuccess('admin.plugin.customurluserpage.regist.success', 'admin');
                }



                return $app->redirect($app->url('admin_customurluserpage'));
            }

            if (!is_null($data['PageLayout'])) {
                $Product = $data['PageLayout'];
            }
        }

        return $this->renderRegistView(
            $app,
            array(
                'form' => $form->createView(),
                //'PageLayout' => $Product
            )
        );
    }
    public function addImage(Application $app, Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException('リクエストが不正です');
        }

        $images = $request->files->get('admin_customurluserpage');

        $files = array();
        if (count($images) > 0) {
            foreach ($images as $img) {
                foreach ($img as $image) {
                    //ファイルフォーマット検証
                    $mimeType = $image->getMimeType();
                    if (strpos($mimeType, 'image')>0) {

                        //throw new UnsupportedMediaTypeHttpException('ファイル形式が不正です');
                    }elseif (strpos($mimeType, 'pdf')>9) {

                    }else{
                        //throw new UnsupportedMediaTypeHttpException('ファイル形式が不正です');                        
                    }
                    $extension = $image->getClientOriginalExtension();
                    $filename = array(
                        'name'=>date('mdHis') . uniqid('_') . '.' . $extension,
                        'dlname'=>$image->getClientOriginalName(),

                        );
                    $image->move($app['config']['image_temp_realdir'], $filename['name']);
                    $files[] = $filename;
                }
            }
        }


        return $app->json(array('files' => $files), 200);
    }

    /**
     * 編集
     * @param Application $app
     * @param Request     $request
     * @param unknown     $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Application $app, Request $request, $id)
    {

        if (is_null($id) || strlen($id) == 0) {
            $app->addError("admin.customurluserpage.customurl_id.notexists", "admin");
            return $app->redirect($app->url('admin_customurluserpage'));
        }

        $service = $app['eccube.plugin.customurluserpage.service.customurluserpage'];

        $CustomUrlUserPage = $app['eccube.plugin.customurluserpage.repository.customurluserpage']->findById($id);

        if (is_null($CustomUrlUserPage)) {
            $app->addError('admin.customurluserpage.notfound', 'admin');
            return $app->redirect($app->url('admin_customurluserpage'));
        }

        $CustomUrlUserPage = $CustomUrlUserPage[0];

        // formの作成
        $form = $app['form.factory']
            ->createBuilder('admin_customurluserpage', $CustomUrlUserPage)
            ->getForm();

        // ファイルの登録
        $images = array();
        $dlfilenames = array();
        $CustomUrlUserPageImages = $CustomUrlUserPage->getCustomUrlUserPageImage();
        foreach ($CustomUrlUserPageImages as $CustomUrlUserPageImage) {
            $images[] = $CustomUrlUserPageImage->getFileName();
            $dlfilenames[] = $CustomUrlUserPageImage->getDlFileName();

        }
        $CustomUrlUserPage->setImages($images);
        $form['images']->setData($images);

        $CustomUrlUserPage->setDlFileNames($dlfilenames);
        $form['dlfilenames']->setData($dlfilenames);

        if ('POST' === $request->getMethod()) {

            $form->handleRequest($request);

            if ($form->isValid()) {

                $status = $service->updateCustomUrlUserPage($form->getData());

                if (!$status) {
                    $app->addError('admin.customurluserpage.notfound', 'admin');
                } else {
                    $app->addSuccess('admin.plugin.customurluserpage.update.success', 'admin');
                }


                // 画像の登録
                $add_images = $form->get('add_images')->getData();
                $add_dlfilenames = $form->get('add_dlfilenames')->getData();
                foreach ($add_images as $k=>$add_image) {
                    $add_dlfilename=$add_dlfilenames[$k];

                    $CustomUrlUserPageImage = new \Plugin\CustomUrlUserPage\Entity\CustomUrlUserPageImage();
                    $CustomUrlUserPageImage
                        ->setFileName($add_image)
                        ->setDlFileName($add_dlfilename)

                        ->setCustomUrlUserPage($CustomUrlUserPage)
                        ->setRank(1);
                    $CustomUrlUserPage->addCustomUrlUserPageImage($CustomUrlUserPageImage);
                    $app['orm.em']->persist($CustomUrlUserPageImage);
                    $app['orm.em']->flush();

                    // 移動
                    $file = new File($app['config']['image_temp_realdir'] . '/' . $add_image);
                    $file->move($app['config']['image_save_realdir']);
                }



                // 画像の削除
                $delete_images = $form->get('delete_images')->getData();
                foreach ($delete_images as $delete_image) {
                    $CustomUrlUserPageImage = $app['eccube.plugin.customurluserpage.repository.customurluserpageimage']
                        ->findOneBy(array('filename' => $delete_image));

                    // 追加してすぐに削除した画像は、Entityに追加されない
                    if ($CustomUrlUserPageImage instanceof \Plugin\CustomUrlUserPage\Entity\CustomUrlUserPageImage) {
                        $CustomUrlUserPage->removeCustomUrlUserPageImage($CustomUrlUserPageImage);
                        $app['orm.em']->remove($CustomUrlUserPageImage);

                    }
                    $app['orm.em']->persist($CustomUrlUserPage);

                    // 削除
                    $fs = new Filesystem();
                    $fs->remove($app['config']['image_save_realdir'] . '/' . $delete_image);
                }
                $app['orm.em']->persist($CustomUrlUserPage);
                $app['orm.em']->flush();


                $ranks = $request->get('rank_images');
                if ($ranks) {
                    foreach ($ranks as $rank) {
                        list($filename, $rank_val) = explode('//', $rank);
                        $CustomUrlUserPageImage = $app['eccube.plugin.customurluserpage.repository.customurluserpageimage']
                            ->findOneBy(array(
                                'filename' => $filename,
                                'CustomUrlUserPage' => $CustomUrlUserPage,
                            ));
                        $CustomUrlUserPageImage->setRank($rank_val);
                        $app['orm.em']->persist($CustomUrlUserPageImage);
                    }
                }
                $app['orm.em']->flush();



                return $app->redirect($app->url('admin_customurluserpage'));
            }
        }

        return $this->renderRegistView(
            $app,
            array(
                'form' => $form->createView(),
                //'PageLayout' => $CustomUrlUserPage->getPageLayout()
            )
        );
    }

    /**
     * @param Application $app
     * @param Request     $request
     * @param unknown     $id
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function delete(Application $app, Request $request, $id)
    {

        $this->isTokenValid($app);

        if (!'POST' === $request->getMethod()) {
            throw new HttpException();
        }
        if (is_null($id) || strlen($id) == 0) {
            $app->addError("admin.customurluserpage.customurl_id.notexists", "admin");
            return $app->redirect($app->url('admin_customurluserpage'));
        }


        $service = $app['eccube.plugin.customurluserpage.service.customurluserpage'];

        // おすすめ商品情報を削除する
        if ($service->deleteCustomUrlUserPage($id)) {
            $app->addSuccess('admin.plugin.customurluserpage.delete.success', 'admin');
        } else {
            $app->addError('admin.customurluserpage.notfound', 'admin');
        }

        return $app->redirect($app->url('admin_customurluserpage'));

    }

    /**
     * 上へ
     * @param Application $app
     * @param Request     $request
     * @param unknown     $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankUp(Application $app, Request $request, $id)
    {

        $this->isTokenValid($app);

        if (is_null($id) || strlen($id) == 0) {
            $app->addError("admin.customurluserpage.customurl_id.notexists", "admin");
            return $app->redirect($app->url('admin_customurluserpage'));
        }

        $service = $app['eccube.plugin.customurluserpage.service.customurluserpage'];

        // IDからおすすめ商品情報を取得する
        $CustomUrlUserPage = $app['eccube.plugin.customurluserpage.repository.customurluserpage']->find($id);
        if (is_null($CustomUrlUserPage)) {
            $app->addError('admin.customurluserpage.notfound', 'admin');
            return $app->redirect($app->url('admin_customurluserpage'));
        }

        // ランクアップ
        $service->rankUp($id);

        $app->addSuccess('admin.plugin.customurluserpage.complete.up', 'admin');

        return $app->redirect($app->url('admin_customurluserpage'));
    }

    /**
     * 下へ
     * @param Application $app
     * @param Request     $request
     * @param unknown     $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rankDown(Application $app, Request $request, $id)
    {

        $this->isTokenValid($app);

        if (is_null($id) || strlen($id) == 0) {
            $app->addError("admin.customurluserpage.customurl_id.notexists", "admin");
            return $app->redirect($app->url('admin_customurluserpage'));
        }

        $service = $app['eccube.plugin.customurluserpage.service.customurluserpage'];

        // IDからおすすめ商品情報を取得する
        $CustomUrlUserPage = $app['eccube.plugin.customurluserpage.repository.customurluserpage']->find($id);
        if (is_null($CustomUrlUserPage)) {
            $app->addError('admin.customurluserpage.notfound', 'admin');
            return $app->redirect($app->url('admin_customurluserpage'));
        }

        // ランクアップ
        $service->rankDown($id);

        $app->addSuccess('admin.plugin.customurluserpage.complete.down', 'admin');

        return $app->redirect($app->url('admin_customurluserpage'));
    }

    /**
     * 編集画面用のrender
     * @param unknown $app
     * @param unknown $parameters
     */
    protected function renderRegistView($app, $parameters = array())
    {
        // 商品検索フォーム
        $searchModalForm = $app['form.factory']->createBuilder('admin_search_pagelayout')->getForm();
        $viewParameters = array(
            'searchPageLayoutModalForm' => $searchModalForm->createView(),
        );
        $viewParameters += $parameters;
        return $app->render('CustomUrlUserPage/Resource/template/admin/regist.twig', $viewParameters);
    }

}
