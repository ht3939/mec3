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
use Eccube\Entity\ProductTag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Eccube\Common\Constant;

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

    /**
     * タグ情報削除
     *
     * @param Application $app
     * @param Request $request
     * @param unknown $id
     */
    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        // del_flg フィルターOFF
        /* @var $softDeleteFilter \Eccube\Doctrine\Filter\SoftDeleteFilter */
        $softDeleteFilter = $app['orm.em']->getFilters()->getFilter('soft_delete');
        $softDeleteFilter->setExcludes(array(
            'Eccube\Entity\Product',
        ));

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

        // 既に削除済み商品のタグを抽出
        /* @var $delqb \Doctrine\ORM\QueryBuilder */
        $delqb = $app['orm.em']->getRepository('Eccube\Entity\ProductTag')->createQueryBuilder('pt');
        $delqb->innerJoin('pt.Product', 'p')
            ->innerJoin('pt.Tag', 'tag')
            ->select('count(pt.id)')
            ->andWhere('p.del_flg = 1')
            ->andWhere('tag.id = :tag_id')
            ->setParameter(':tag_id', $id);

        $delCount = $delqb->getQuery()->getSingleScalarResult();

        if($count > 0 && $count != $delCount) {
            // 削除対象のタグを利用している商品が存在する。
            $app->addError('現在商品に利用されているため削除できませんでした。', 'admin');
            return $app->redirect($app->url('admin_product_tag_ex'));
        }

        // 削除済み商品に紐づくタグを削除
        if($count == $delCount) {
            $removeSql = "delete from dtb_product_tag where tag = :tag;";
            $app['orm.em']->getConnection()->executeUpdate($removeSql, array('tag' => $id));
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
            $app->addError('タグの削除に失敗しました', 'admin');
        }

        return $app->redirect($app->url('admin_product_tag_ex'));
    }

    /**
     * ランク変更
     *
     * @param Application $app
     * @param Request $request
     * @return boolean
     */
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

    /**
     * タグ一括設定
     *
     * @param Application $app
     * @param Request $request
     */
    public function groupUpdate(Application $app, Request $request)
    {

        $this->app = $app;

        $builder = $app['form.factory']->createBuilder('admin_update_tag');

        $form = $builder->getForm();

        if($request->isXmlHttpRequest()) {

            if ('POST' === $request->getMethod()) {

                // タイムアウトを無効にする.
                set_time_limit(0);
                ini_set('memory_limit', '512M');

                $form->handleRequest($request);

                if (!$form->isValid()) {
                    // エラー
                    $arrResult = array();
                    $arrResult['ret'] = 0;

                    $response = new Response(json_encode($arrResult));
                    $response->headers->set('Content-Type', 'application/json');

                    return $response;
                }

                // 対象の商品を取得
                $session = $app['session'];
                $searchData = $session->get('eccube.admin.product.search');

                // ページ条件制御
                $status = $request->get('status');
                if ($status == $app['config']['admin_product_stock_status']) {
                    $searchData['stock_status'] = Constant::DISABLED;
                }

                $app['eccube.repository.master.disp']->findAll();

                $qb = $app['eccube.repository.product']->getQueryBuilderBySearchDataForAdmin($searchData);

                // プラグインでの検索条件拡張を考慮してフックポイントを設置
                $event = new EventArgs(
                    array(
                        'qb' => $qb,
                        'searchData' => $searchData,
                    ),
                    $request
                );
                $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_PRODUCT_INDEX_SEARCH, $event);
                $searchData = $event->getArgument('searchData');

                $query = $qb->getQuery();

                // 結果
                $arrResult = array();

                $update_count = 0;

                switch ($request->get('mode')) {
                    case 'update':
                        // 変更
                        $update_count = $this->tagUpdate($app, $form, $query);
                        break;
                    case 'insert':
                        // 追加
                        $update_count = $this->tagInsert($app, $form, $query);
                        break;
                    case 'delete':
                        // 削除
                        $update_count = $this->tagDelete($app, $form, $query);
                        break;
                    default:
                        // 変更
                        $update_count = $this->tagUpdate($app, $form, $query);
                        break;
                }

                $arrResult['ret'] = 1;
                $arrResult['update_count'] = $update_count;

                $response = new Response(json_encode($arrResult));
                $response->headers->set('Content-Type', 'application/json');

                return $response;

            } else {


                $title = 'タグ一括設定変更';
                $sub_title = '一括設定を行うタグを選択してください';
                $detail = "※現在設定されているタグがクリアされ、選択したタグが反映されます。";
                $mode = "update";
                $button = "一括変更";

                $tagexUpdateMode = $request->get('tagex_update_mode');

                switch ($tagexUpdateMode) {
                    case 2:
                        // 追加
                        $title = 'タグ一括追加';
                        $sub_title = '一括追加を行うタグを選択してください';
                        $detail = "※現在設定されているタグはそのまま、選択したタグが追加されます。";
                        $mode = "insert";
                        $button = "一括追加";
                        break;
                    case 3:
                        // 削除
                        $title = 'タグ一括削除';
                        $sub_title = '一括削除を行うタグを選択してください';
                        $detail = "※現在設定されているタグから、選択したタグが削除されます。";
                        $mode = "delete";
                        $button = "一括削除";
                        break;
                    case 1:
                        // 一括変更
                    default:
                        // デフォルト設定を反映
                        break;
                }

                // タグ情報返却
                $view = 'TagEx/Resource/template/admin/Product/tagex_view.twig';
                return $app->render($view, array(
                    'title' => $title,
                    'sub_title' => $sub_title,
                    'detail' => $detail,
                    'form' => $form->createView(),
                    'mode' => $mode,
                    'button' => $button,
                ));

            }

        }

        return false;

    }

    private function getRemoveSql() {

        $removeSql = "delete from dtb_product_tag where product_tag_id = :product_tag_id;";
        return $removeSql;
    }

    private function getInsertSql($app) {

        $insertSql = "insert into dtb_product_tag ";
        if($app['config']['database']['driver'] == 'pdo_pgsql') {
            $insertSql .= " (product_tag_id, product_id, tag, creator_id, create_date) ";
            $insertSql .= " values ( ";
            $insertSql .= " nextval('dtb_product_tag_product_tag_id_seq'), :product_id, :tag, :creator_id, :create_date);";
        } else {
            $insertSql .= " (product_id, tag, creator_id, create_date) ";
            $insertSql .= " values ( ";
            $insertSql .= " :product_id, :tag, :creator_id, :create_date);";
        }

        return $insertSql;
    }

    /**
     * タグ情報一括設定
     *
     * @param unknown $app
     * @param unknown $query
     */
    private function tagUpdate($app, $form, $query) {

        // タグ情報更新
        $Tags = $form->get('Tag')->getData();

        // タグIDリスト作成
        /* @var $Tag \Eccube\Entity\Master\Tag */
        $arrTagData = array();
        foreach ($Tags as $Tag) {
            $arrTagData[$Tag->getId()] = $Tag;
        }

        // SQL生成
        $removeSql = $this->getRemoveSql();
        $arrRemove = array();

        $insertSql = $this->getInsertSql($app);
        $arrInsert = array();

        /* @var $Member \Eccube\Entity\Member */
        $Member = $app['security']->getToken()->getUser();
        $creater_id = $Member->getCreator()->getId();

        $date = new \DateTime();
        $create_date = $date->format('Y-m-d H:i:s');

        $this->em = $app['orm.em'];
        // SQL実行
        $this->em->getConfiguration()->setSQLLogger(null);

        $count = 0;

        /* @var $Product \Eccube\Entity\Product */
        foreach ($query->getResult() as $Product) {

            $arrTag = $arrTagData;

            // タグのクリア
            $ProductTags = $Product->getProductTag();

            /* @var $ProductTag \Eccube\Entity\ProductTag */
            foreach ($ProductTags as $ProductTag) {

                if(isset($arrTag[$ProductTag->getTagId()])) {
                    // 既に存在するので削除からも登録からも除外
                    unset($arrTag[$ProductTag->getTagId()]);
                    continue;
                }

                $arrRemove[] = $ProductTag->getId();
            }

            // タグの登録
            if(!empty($Tags)) {

                /* @var $ProductTag \Eccube\Entity\ProductTag */
                foreach ($arrTag as $Tag) {
                    $arrInsert[] = array(
                        'product_id' => $Product->getId(),
                        'tag' => $Tag->getId(),
                        'creator_id' => $creater_id,
                        'create_date' => $create_date,
                    );
                }

            }

            $this->em->detach($Product);
            $this->em->clear();
            $query->free();

            $count++;
        }

        $this->em->getConnection()->beginTransaction();

        if(count($arrRemove) > 0) {
            foreach ($arrRemove as $remove_id) {
                $this->em->getConnection()->executeUpdate($removeSql, array(':product_tag_id' => $remove_id));
            }
        }

        $this->em->getConnection()->commit();

        $this->em->getConnection()->beginTransaction();

        if(count($arrInsert) > 0) {
            foreach ($arrInsert as $insertData) {
                $this->em->getConnection()->executeUpdate($insertSql, $insertData);
            }
        }

        $this->em->getConnection()->commit();

        return $count;
    }

    /**
     *
     * @param unknown $app
     * @param unknown $form
     * @param unknown $query
     */
    private function tagInsert($app, $form, $query) {

        // タグ追加
        $Tags = $form->get('Tag')->getData();

        if(empty($Tags)) {
            return;
        }

        // SQL生成
        $insertSql = $this->getInsertSql($app);
        $arrInsert = array();

        /* @var $Member \Eccube\Entity\Member */
        $Member = $app['security']->getToken()->getUser();
        $creater_id = $Member->getCreator()->getId();

        $date = new \DateTime();
        $create_date = $date->format('Y-m-d H:i:s');

        // タグIDリスト作成
        /* @var $Tag \Eccube\Entity\Master\Tag */
        $arrTagData = array();
        foreach ($Tags as $Tag) {
            $arrTagData[$Tag->getId()] = $Tag;
        }

        // SQL実行
        $this->em = $app['orm.em'];
        $this->em->getConfiguration()->setSQLLogger(null);

        $count = 0;

        /* @var $Product \Eccube\Entity\Product */
        foreach ($query->getResult() as $Product) {

            $arrTag = $arrTagData;

            // タグの存在チェック
            $ProductTags = $Product->getProductTag();

            /* @var $ProductTag \Eccube\Entity\ProductTag */
            foreach ($ProductTags as $ProductTag) {

                if(isset($arrTag[$ProductTag->getTagId()])) {
                    unset($arrTag[$ProductTag->getTagId()]);
                }
            }

            // タグの登録
            foreach ($arrTag as $Tag) {

                $arrInsert[] = array(
                    'product_id' => $Product->getId(),
                    'tag' => $Tag->getId(),
                    'creator_id' => $creater_id,
                    'create_date' => $create_date,
                );
            }

            $this->em->detach($Product);
            $this->em->clear();
            $query->free();

            $count++;
        }

        $this->em->getConnection()->beginTransaction();

        if(count($arrInsert) > 0) {
            foreach ($arrInsert as $insertData) {
                $this->em->getConnection()->executeUpdate($insertSql, $insertData);
            }
        }

        $this->em->getConnection()->commit();

        return $count;
    }

    /**
     * タグ情報一括削除
     *
     * @param unknown $app
     * @param unknown $form
     * @param unknown $query
     */
    private function tagDelete($app, $form, $query) {

        // タグ追加
        $Tags = $form->get('Tag')->getData();

        if(empty($Tags)) {
            return;
        }

        // SQL生成
        $removeSql = $this->getRemoveSql();
        $arrRemove = array();

        /* @var $Product \Eccube\Entity\Product */
        $arrTagId = array();
        foreach ($Tags as $Tag) {
            $arrTagId[$Tag->getId()] = $Tag->getId();
        }

        // SQL実行
        $this->em = $app['orm.em'];
        $this->em->getConfiguration()->setSQLLogger(null);

        $count = 0;

        foreach ($query->getResult() as $Product) {

            // タグのクリア
            $ProductTags = $Product->getProductTag();

            if(empty($ProductTags)) {
                continue;
            }

            /* @var $ProductTag \Eccube\Entity\ProductTag */
            foreach ($ProductTags as $ProductTag) {

                if(empty($ProductTag)) {
                    continue;
                }

                if(isset($arrTagId[$ProductTag->getTagId()])) {
                    // 削除対象
                    $arrRemove[] = $ProductTag->getId();
                }

            }

            $this->em->detach($Product);
            $this->em->clear();
            $query->free();

            $count++;
        }

        $this->em->getConnection()->beginTransaction();

        if(count($arrRemove) > 0) {
            foreach ($arrRemove as $remove_id) {
                $this->em->getConnection()->executeUpdate($removeSql, array(':product_tag_id' => $remove_id));
            }
        }

        $this->em->getConnection()->commit();

        return $count;
    }

}
