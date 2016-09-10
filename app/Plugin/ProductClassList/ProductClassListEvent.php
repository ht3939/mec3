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
          'ProductClassList/Resource/template/product_class_list2.twig',
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
          ->filter('#item_detail');

      $oldHtml = $oldElement->html();
      $newHtml = $twig;

      $html = $crawler->html();
      $html = str_replace($oldHtml, $newHtml, $html);

      $html = html_entity_decode($html, ENT_NOQUOTES, 'UTF-8');

      $first = array("<head>", "</body>");
      $last = array("<html lang=\"ja\"><head>", "</body></html>");
      $html = str_replace($first, $last, $html);

      $response->setContent($html);
      $event->setResponse($response);

    }



    public function addCartBefore()
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
                  }

                  $response = $app->redirect($app->url('product_detail', array('productId' => $Product->getId())));
                  $response->setContent(null);
                  $response->send();
                  exit;


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


          if ($app['security']->isGranted('ROLE_USER')) {
              $Customer = $app['security']->getToken()->getUser();
              $is_favorite = $app['eccube.repository.customer_favorite_product']->isFavorite($Customer, $Product);
          } else {
              $is_favorite = false;
          }

          exit ($app->render('ProductClassList/Resource/template/product_class_list.twig', array(
              'title' => $app['request']->get('title'),
              'subtitle' => $Product->getName(),
              'form' => $form->createView(),
              'Product' => $Product,
              'is_favorite' => $is_favorite,
          )));
      }

    }


}
