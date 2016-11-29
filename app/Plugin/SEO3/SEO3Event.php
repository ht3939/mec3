<?php

/*
 * This file is part of the SEO3
 *
 * Copyright (C) 2016 Nobuhiko Kimoto
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\SEO3;

use Eccube\Event\EventArgs;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\DomCrawler\Crawler;
use Plugin\SEO3\Entity\Seo;

class SEO3Event
{

    /** @var  \Eccube\Application $app */
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }


    public function onAdminProductEditInitialize(EventArgs $event) {

        $Product = $event->getArgument('Product');
        $id = $Product->getId();

        $meta = null;
        if ($id) {
            // カテゴリ編集時は初期値を取得
            $meta = $this->app['eccube.plugin.seo3.repository.seo']->findOneByProductId($id);
        }
        // コンテンツが未登録の場合
        if (!$meta) {
            $meta = new Seo();
        }

        $this->createForm($event, $meta);
    }

    public function onAdminProductEditComplete(EventArgs $event)
    {
        /** @var Category $target_category */
        $Product = $event->getArgument('Product');
        /** @var FormInterface $form */
        $form = $event->getArgument('form');
        // 現在のエンティティを取得
        $id = $Product->getId();
        // product_idからデータを取得
        $meta = $this->app['eccube.plugin.seo3.repository.seo']->findOneByProductId($id);
        if (is_null($meta)) {
            $meta = new Seo();
        }

        // エンティティを更新
        $meta
            ->setProductId($id)
            ->setDescription($form['plg_description']->getData())
            ->setKeywords($form['plg_keywords']->getData())
            ->setTitle($form['plg_title']->getData())
            ;
        // DB更新
        $this->app['orm.em']->persist($meta);
        $this->app['orm.em']->flush($meta);
    }

    public function onAdminProductCategoryIndexInitialize(EventArgs $event)
    {
        /** @var Category $target_category */
        $TargetCategory = $event->getArgument('TargetCategory');
        $id = $TargetCategory->getId();
        $meta = null;
        if ($id) {
            // カテゴリ編集時は初期値を取得
            $meta = $this->app['eccube.plugin.seo3.repository.seo']->findOneByCategoryId($id);
        }
        // コンテンツが未登録の場合
        if (!$meta) {
            $meta = new Seo();
        }

        $this->createForm($event, $meta);
    }

    /**
     * 管理画面：カテゴリ登録画面で、登録処理を行う.
     *
     * @param EventArgs $event
     */
    public function onAdminProductCategoryEditComplete(EventArgs $event)
    {
        /** @var Category $target_category */
        $TargetCategory = $event->getArgument('TargetCategory');
        /** @var FormInterface $form */
        $form = $event->getArgument('form');
        // 現在のエンティティを取得
        $id = $TargetCategory->getId();

        // category_idからデータを取得
        $meta = $this->app['eccube.plugin.seo3.repository.seo']->findOneByCategoryId($id);
        if (is_null($meta)) {
            $meta = new Seo();
        }

        // エンティティを更新
        $meta
            ->setCategoryId($id)
            ->setDescription($form['plg_description']->getData())
            ->setKeywords($form['plg_keywords']->getData())
            ->setTitle($form['plg_title']->getData())
            ;
        // DB更新
        $this->app['orm.em']->persist($meta);
        $this->app['orm.em']->flush($meta);
    }

    public function onRenderFrontPage(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        $html = $response->getContent();
        // todo page_idの取り方が分かれば基本的には全ページ行ける
        $meta = null;

        // 一覧
        if ($id = $request->query->get('category_id')) {
            $meta = $this->app['eccube.plugin.seo3.repository.seo']->findOneByCategoryId($id);
            // 詳細
        } else if ($id = $request->attributes->get('id')) {
            $meta = $this->app['eccube.plugin.seo3.repository.seo']->findOneByProductId($id);
        } else if ($request->attributes->get('_route') == 'homepage') {
            $db = @unserialize(@file_get_contents(__DIR__ . '/Resource/db.txt'));
            if (!empty($db['title'])) {
                $meta['title'] = $db['title'];
            }
        }

        if (!$meta) return;

        // twigを足す
        $addContents = $this->app->renderView(
            'SEO3/Resource/template/meta.twig',
            array(
                'meta' => $meta,
            )
        );

        $crawler = new Crawler($html);

        $oldHtml = $crawler->filter('head')->html();
        $oldHtml = html_entity_decode($oldHtml, ENT_NOQUOTES, 'UTF-8');

        // titleタグを除去する機能
        if ($meta['title']) {
            $reg = '/<title.*?>.*?<\/title>/mis';
            $newHtml = preg_replace($reg, '', $oldHtml);
        } else {
            $newHtml = $oldHtml;
        }

        if (isset($meta['description'])) {
            $reg = '/<meta name="description".*?>/mis';
            $newHtml = preg_replace($reg, '', $newHtml);
        }
        if (isset($meta['keywords'])) {
            $reg = '/<meta name="keywords".*?>/mis';
            $newHtml = preg_replace($reg, '', $newHtml);
        }

        $newHtml .= "\n".$addContents;

        $html = $this->getHtml($crawler);
        $html = str_replace($oldHtml, $newHtml, $html);

        $response->setContent($html);
        $event->setResponse($response);
    }


    /**
     * 解析用HTMLを取得
     *
     * @see https://github.com/EC-CUBE/related-product-plugin/blob/master/Event.php
     * @param Crawler $crawler
     * @return string
     */
    private function getHtml(Crawler $crawler)
    {
        $html = '';
        foreach ($crawler as $domElement) {
            $domElement->ownerDocument->formatOutput = true;
            $html .= $domElement->ownerDocument->saveHTML();
        }
        return html_entity_decode($html, ENT_NOQUOTES, 'UTF-8');
    }


    private function createForm($event, $meta) {

        // フォームの追加
        /** @var FormInterface $builder */
        $builder = $event->getArgument('builder');

        $builder->add(
            'plg_title',
            'text',
            array(
                'required' => false,
                'label' => false,
                'mapped' => false,
                'attr' => array(
                    'placeholder' => 'title (最大32文字程度入力)',
                ),
            )
        );


        $builder->add(
            'plg_description',
            'text',
            array(
                'required' => false,
                'label' => false,
                'mapped' => false,
                'attr' => array(
                    'placeholder' => 'description (最大124文字程度入力)',
                ),
            )
        );

        $builder->add(
            'plg_keywords',
            'text',
            array(
                'required' => false,
                'label' => false,
                'mapped' => false,
                'attr' => array(
                    'placeholder' => 'keywords (, 区切り)',
                ),
            )
        );

        // 初期値を設定
        $builder->get('plg_title')->setData($meta->getTitle());
        $builder->get('plg_description')->setData($meta->getDescription());
        $builder->get('plg_keywords')->setData($meta->getKeywords());
    }

}
