<?php
/*
 * Copyright(c) 2015 SystemFriend Inc. All rights reserved.
 * http://ec-cube.systemfriend.co.jp/
 */

namespace Plugin\ExcludeProductPayment\HookPoint;

use Eccube\Application;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * html置き換えのサービス
 */
class HookBaseService
{
    protected $app;
    protected $const;

    /**
     * コンストラクタ
     *
     * @param Application $app
     */
    function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
        /* @var $Setting \Plugin\ExcludeProductPayment\Service\ConfigService */
        $Setting     = $this->app['eccube.plugin.service.epp.config'];
        $this->const = $Setting->getConst();
    }

    /**
     * 後に挿入
     *
     * @param string $html
     * @param Crawler $oldElement
     * @param string $twig
     * @param RedirectResponse $response
     */
    public function insertAfter(Crawler $crawler, Crawler $oldElement, $twig, Response &$response)
    {
        if ($oldElement->count() > 0) {
            $oldHtml = $oldElement->html();
            $newHtml = $oldHtml.$twig;

            $changehtml = str_replace($oldHtml, $newHtml, $crawler->html());

            $response->setContent($changehtml);
        }
    }

    /**
     * 後に挿入(tableの各行版)
     *
     * 要素ごとですると同じものが存在する可能性が高いため
     * tableではない場合でも同じものが存在する場合、$trElementを大きいくくりで取得すればこの関数でOK
     *
     * @param Crawler $trElement
     * @param Crawler $oldElement
     * @param string $twig
     * @param RedirectResponse $response
     */
    public function insertAfterTableRow(Crawler $crawler, Crawler $trElement, Crawler $oldElement, $twig, Response &$response)
    {
        if ($trElement->count() > 0 && $oldElement->count() > 0) {
            $trHtml  = $trElement->html();
            $oldHtml = $oldElement->html();
            $newHtml = $oldHtml.$twig;

            $newTrHtml  = str_replace($oldHtml, $newHtml, $trHtml);
            $changehtml = str_replace($trHtml, $newTrHtml, $crawler->html());

            $response->setContent($changehtml);
        }
    }

    /**
     * 要素の差し込み(前)
     *
     * @param string $html 変換元HTML
     * @param string $expression
     * @param integer $index
     * @param string $twig 変換用HTML
     * @return string $html 変換後HTML
     */
    public function appendBefor($html, $expression, $index, $twig)
    {
        $crawler = new Crawler($html);
        $html = $crawler->html();

        $oldElement = $crawler->filter($expression)->eq($index);
        if (!$oldElement->count()) return $html;

        $oldHtml = $oldElement->html();
        $newHtml = $twig.$oldHtml;

        $html = str_replace($oldHtml, $newHtml, $html);

        return $html;
    }

    /**
     * 要素の差し込み(後)
     *
     * @param string $html 変換元HTML
     * @param string $expression
     * @param integer $index
     * @param string $twig 変換用HTML
     * @return string $html 変換後HTML
     */
    public function appendAfter($html, $expression, $index, $twig)
    {
        $crawler = new Crawler($html);
        $html = $crawler->html();

        $oldElement = $crawler->filter($expression)->eq($index);
        if (!$oldElement->count()) return $html;

        $oldHtml = $oldElement->html();
        $newHtml = $oldHtml.$twig;

        $html = str_replace($oldHtml, $newHtml, $html);

        return $html;
    }

    /**
     * 子要素の置き換え
     *
     * @param string $html 変換元HTML
     * @param string $expression
     * @param integer $index
     * @param string $twig 変換用HTML
     * @return string $html 変換後HTML
     */
    public function replaceNodeChild($html, $expression, $index, $twig)
    {
        $crawler = new Crawler($html);
        $html = $crawler->html();

        $oldElement = $crawler->filter($expression)->eq($index);
        if (!$oldElement->count()) return $html;

        $oldHtml = $oldElement->html();
        $newHtml = $twig;

        $html = str_replace($oldHtml, $newHtml, $html);

        return $html;
    }

    /**
     *  サイト内リダイレクト
     *
     * @param string $bind_name
     * @param array $parameters
     * @param int $status
     */
    public function redirectSiteUrl($bind_name, $parameters=array(), $status = 302)
    {
        $this->redirectUrl($this->app->url($bind_name, $parameters), $status);
    }

    /**
     * 強制リダイレクト
     *
     * @param string $url
     * @param string $status リダイレクトステータス
     */
    public function redirectUrl($url, $status = 302)
    {
        header("Location: " . $url, true, $status);
        exit;
    }

    /**
     * 新規作成時のデータのID取得
     *
     * @param FilterResponseEvent $event
     * @param string $bind_name
     * @param string $target_name
     * @param string $comp_target
     * @return targetの値
     */
    public function getTarget($event, $bind_name, $target_name='id', $comp_target='0')
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        if ($request->attributes->get($target_name)) {
            $id = $request->attributes->get($target_name);
        } else {
            $location = explode('/', $response->headers->get('location'));
            $url = explode('/', $this->app->url($bind_name, array($target_name => $comp_target)));
            $diffs = array_values(array_diff($location, $url));
            if (!empty($diffs[0])) {
                $id = $diffs[0];
            }
        }

        return $id;
    }
}
