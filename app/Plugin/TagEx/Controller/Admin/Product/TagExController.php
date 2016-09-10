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


namespace Plugin\TagEx\Controller\Admin\Product;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\CsvType;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Entity\Master\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TagExController extends AbstractController
{
    public function index(Application $app, Request $request, $id = null)
    {

        // マスターとのズレを修正
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $app['orm.em']->getRepository('Eccube\Entity\Master\Tag')
                            ->createQueryBuilder('mtag');

        /* @var $subqb \Doctrine\ORM\QueryBuilder */
        $subqb = $app['orm.em']->getRepository('Plugin\TagEx\Entity\TagEx')
                            ->createQueryBuilder('tagex')
                            ->innerJoin('tagex.Tag', 'tag')
                            ->select('tag.id');

        $qb->andWhere($qb->expr()->notIn('mtag.id', $subqb->getDQL()));
        $arrTag = $qb->getQuery()->getResult();

        /* @var $Tag Eccube\Entity\Master\Tag */
        $adjustFlg = false;
        $em = $app['orm.em'];
        foreach ($arrTag as $Tag) {
            // TagEx生成
            $TagEx = new \Plugin\TagEx\Entity\TagEx();
            $TagEx->setTag($Tag);
            $em->persist($TagEx);
            $em->flush();

            $adjustFlg = true;
        }

        if($adjustFlg) {
//             $app->addInfo('マスターデータとの整合性を調整しました。', 'admin');
        }


        $builder = $app['form.factory']->createBuilder('admin_tag_ex');

        $form = $builder->getForm();

        if (!empty($id) && $id) {
            /* @var $TargetTagEx \Plugin\TagEx\Entity\TagEx */
            $TargetTagEx = $app['tagex.repository.tagex']->findTagEx($id);
            $form->get('name')->setData($TargetTagEx->getTag()->getName());

        } else {
            $TargetTagEx = new \Plugin\TagEx\Entity\TagEx();
            $TargetTagEx->setTag(new \Eccube\Entity\Master\Tag());
        }

        // タグ一覧取得
        $arrTagEx = $app['tagex.repository.tagex']->findTagEx();

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {

                $formData = $form->getData();

                $errFlg = false;

                // タグ名称の重複チェック
                if(empty($id)) {
                    /* @var $TagEx \Plugin\TagEx\Entity\TagEx */
                    foreach ($arrTagEx as $TagEx) {
                        if($TagEx->getTag()->getName() == $formData['name']) {
                            // 既に同じ名称が存在する
                            $app->addError('同じ名称のタグが既に存在します。', 'admin');
                            $errFlg = true;
                            break;
                        }
                    }
                }

                if(!$errFlg) {

                    $TargetTagEx->getTag()->setName($formData['name']);

                    $status = $app['tagex.repository.tagex']->save($TargetTagEx);

                    if ($status) {
                        $app->addSuccess('タグを保存しました。', 'admin');
                        return $app->redirect($app->url('admin_product_tag_ex'));

                    } else {
                        $app->addError('タグを保存できませんでした。', 'admin');
                    }

                }
            }
        }

        return $app->render('TagEx/Resource/template/admin/Product/tagex.twig', array(
            'form' => $form->createView(),
            'TagExs' => $arrTagEx,
            'TargetTagEx' => $TargetTagEx,
        ));
    }

    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        // 削除可否チェック
        // 該当するタグを有する商品が存在するか
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $app['orm.em']->getRepository('Eccube\Entity\ProductTag')->createQueryBuilder('pt');
        $qb->innerJoin('pt.Product', 'p')
            ->innerJoin('pt.Tag', 'tag')
            ->select('count(pt.id)')
            ->andWhere('tag.id = :tag_id')
            ->setParameter(':tag_id', $id);

        $count = $qb->getQuery()->getSingleScalarResult();

        if($count > 0) {
            // 削除対象のタグを利用している商品が存在する。
            $app->addError('現在商品に利用されているため削除できませんでした。', 'admin');
            return $app->redirect($app->url('admin_product_tag_ex'));
        }


        $TargetTagEx = $app['tagex.repository.tagex']->findTagEx($id);
        if (!$TargetTagEx) {
            $app->deleteMessage();
            return $app->redirect($app->url('admin_product_tag_ex'));
        }

        // 削除処理
        $status = $app['tagex.repository.tagex']->delete($TargetTagEx);

        if ($status === true) {

            $app->addSuccess('タグを削除しました。', 'admin');
        } else {
            $app->addError('admin.category.delete.error', 'admin');
        }

        return $app->redirect($app->url('admin_product_tag_ex'));
    }

    public function moveRank(Application $app, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $ranks = $request->request->all();
            foreach ($ranks as $tagId => $rank) {
                /* @var $Tag \Eccube\Entity\Master\Tag */
                $Tag = $app['eccube.repository.master.tag']
                    ->find($tagId);
                $Tag->setRank($rank);
                $app['orm.em']->persist($Tag);
            }
            $app['orm.em']->flush();
        }
        return true;
    }

}
