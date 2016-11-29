<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2015 Takashi Otaki All Rights Reserved.
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\ProductClassList;

use Eccube\Application;
use Eccube\Exception\CartException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Eccube\Common\Constant;

class ProductClassListEvent
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    // フロント：商品詳細画面に規格一覧を表示
    public function productClassListBefore(FilterResponseEvent $event)
    {
      $app = $this->app;

      if ($app['request']->getMethod() !== 'POST') {

        if ($app['config']['nostock_hidden']) {
            $app['orm.em']->getFilters()->enable('nostock_hidden');
        }

        $id = $app['request']->get('id');

        /* @var $Product \Eccube\Entity\Product */
        $Product = $app['eccube.repository.product']->get($id);
        if (!$app['request']->getSession()->has('_security_admin') && $Product->getStatus()->getId() !== 1) {
            throw new NotFoundHttpException();
        }

        $form = $app['form.factory']
            ->createNamedBuilder('', 'product_class_list', null, array(
              'product' => $Product,
              'id_add_product_id' => false,
            ))
            ->getForm();

        if ($app['security']->isGranted('ROLE_USER')) {
            $Customer = $app['security']->getToken()->getUser();
            $is_favorite = $app['eccube.repository.customer_favorite_product']->isFavorite($Customer, $Product);
        } else {
            $is_favorite = false;
        }

        $twig = $app->renderView(
            'ProductClassList/Resource/template/product_class_list.twig',
            array(
              'title' => $app['request']->get('title'),
              'subtitle' => $Product->getName(),
              'form' => $form->createView(),
              'Product' => $Product,
              'is_favorite' => $is_favorite,
            )
        );

        $response = $event->getResponse();

        $html = $response->getContent();
        $crawler = new Crawler($html);

        $oldElement = $crawler
            ->filter('#form1');

        $oldHtml = $oldElement->html();
        $oldHtml = html_entity_decode($oldHtml, ENT_NOQUOTES, 'UTF-8');
        $newHtml = $twig;

        $html = $this->getHtml($crawler);
        $html = str_replace($oldHtml, $newHtml, $html);

        $response->setContent($html);
        $event->setResponse($response);

      }

    }


    /**
     * v3.0.9以降の処理
     *
     */

    public function addCartBefore($event = null)
    {
      $app = $this->app;

      if ($app['request']->getMethod() === 'POST') {

      $id = $app['request']->get('id');

      /* @var $Product \Eccube\Entity\Product */
      $Product = $app['eccube.repository.product']->get($id);
      if (!$app['request']->getSession()->has('_security_admin') && $Product->getStatus()->getId() !== 1) {
          throw new NotFoundHttpException();
      }

      $form = $app['form.factory']
          ->createNamedBuilder('', 'product_class_list', null, array(
            'product' => $Product,
            'id_add_product_id' => false,
          ))
          ->getForm();
          $form->handleRequest($app['request']);

          if ($form->isValid()) {
              $addCartData = $form->getData();
              if ($addCartData['mode'] === 'add_favorite') {
                  if ($app['security']->isGranted('ROLE_USER')) {
                      $Customer = $app['security']->getToken()->getUser();
                      $app['eccube.repository.customer_favorite_product']->addFavorite($Customer, $Product);
                      $app['session']->getFlashBag()->set('product_detail.just_added_favorite', $Product->getId());

                      // 3.0.9以降
                      $response = $app->redirect($app->url('product_detail', array('id' => $Product->getId())));
                      $event->setResponse($response);
                      return;

                    } else {
                      // 非会員の場合、ログイン画面を表示
                      //  ログイン後の画面遷移先を設定
                      $app->setLoginTargetPath($app->url('product_detail', array('id' => $Product->getId())));
                      $app['session']->getFlashBag()->set('eccube.add.favorite', true);
                      $response = $app->redirect($app->url('mypage_login'));
                      $event->setResponse($response);
                      return;
                    }

              } else {

                  try {

                    if ($Product->getClassName1()) {
                      $app['eccube.service.cart']->addProduct($addCartData['product_class'],
                      $addCartData['quantity'])->save();
                    } else {
                      $app['eccube.service.cart']->addProduct($addCartData['product_class_id'],
                      $addCartData['quantity'])->save();
                    }


                  } catch (CartException $e) {
                      $app->addRequestError($e->getMessage());
                  }

                    $response = $app->redirect($app->url('cart'));
                    $event->setResponse($response);
                    return;

              }
          }
      }
    }


    /**
     * v3.0.8までで使用
     *
     */
    public function onControlleraddCartAfter()
    {

      if ($this->supportNewHookPoint()) {
          return;
      }

      $app = $this->app;

      if ($app['request']->getMethod() === 'POST') {

      $id = $app['request']->get('id');

      /* @var $Product \Eccube\Entity\Product */
      $Product = $app['eccube.repository.product']->get($id);
      if (!$app['request']->getSession()->has('_security_admin') && $Product->getStatus()->getId() !== 1) {
          throw new NotFoundHttpException();
      }

      $form = $app['form.factory']
          ->createNamedBuilder('', 'product_class_list', null, array(
            'product' => $Product,
            'id_add_product_id' => false,
          ))
          ->getForm();
          $form->handleRequest($app['request']);

          if ($form->isValid()) {
              $addCartData = $form->getData();
              if ($addCartData['mode'] === 'add_favorite') {
                  if ($app['security']->isGranted('ROLE_USER')) {
                      $Customer = $app['security']->getToken()->getUser();
                      $app['eccube.repository.customer_favorite_product']->addFavorite($Customer, $Product);
                      $app['session']->getFlashBag()->set('product_detail.just_added_favorite', $Product->getId());

                      // 3.0.8以前
                      $response = $app->redirect($app->url('product_detail', array('id' => $Product->getId())));
                      $response->setContent(null);
                      $response->send();
                      exit;

                    } else {
                      // 非会員の場合、ログイン画面を表示
                      //  ログイン後の画面遷移先を設定
                      $app->setLoginTargetPath($app->url('product_detail', array('id' => $Product->getId())));
                      $app['session']->getFlashBag()->set('eccube.add.favorite', true);
                      $response = $app->redirect($app->url('mypage_login'));
                      $response->setContent(null);
                      $response->send();
                      exit;
                    }

              } else {

                  try {

                    if ($Product->getClassName1()) {
                      $app['eccube.service.cart']->addProduct($addCartData['product_class'],
                      $addCartData['quantity'])->save();
                    } else {
                      $app['eccube.service.cart']->addProduct($addCartData['product_class_id'],
                      $addCartData['quantity'])->save();
                    }


                  } catch (CartException $e) {
                      $app->addRequestError($e->getMessage());
                  }

                    $response = $app->redirect($app->url('cart'));
                    $response->setContent(null);
                    $response->send();
                    exit;

              }
          }
      }

    }


    /**
     * 解析用HTMLを取得
     *
     * @param Crawler $crawler
     * @return string
     */
    private function getHtml(Crawler $crawler)
    {
        $html = '';
        /** @var \DOMElement $domElement */
        foreach ($crawler as $domElement) {
            $domElement->ownerDocument->formatOutput = true;
            $html .= $domElement->ownerDocument->saveHTML();
        }

        return html_entity_decode($html, ENT_NOQUOTES, 'UTF-8');
    }


    /**
     * v3.0.9以降のフックポイントに対応しているのか
     *
     * @return bool
     */
    private function supportNewHookPoint()
    {
        return version_compare('3.0.9', Constant::VERSION, '<=');
    }


}
