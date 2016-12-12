<?php
/*
 * Copyright(c) 2015 SystemFriend Inc. All rights reserved.
 * http://ec-cube.systemfriend.co.jp/
 */

namespace Plugin\ExcludeProductPayment\Service;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\Util\SecureRandom;
use Monolog\Logger;

class UtilService
{
    protected $app;
    protected $const;

    public function __construct(\Eccube\Application $app)
    {
        $this->app   = $app;
        /* @var $Setting \Plugin\ExcludeProductPayment\Service\ConfigService */
        $Setting     = $this->app['eccube.plugin.service.cpr.config'];
        $this->const = $Setting->getConst();
    }

    /**
     * リダイレクト先商品IDを取得
     *
     * @param $product_id
     * @return リダイレクト先商品ID 　空白の場合、エラーもしくは設定されていない
     */
    public function getRedirectProductId($product_id){
        $app   = $this->app;
        $const = $this->const;

        /* @var $Setting \Plugin\ExcludeProductPayment\Service\ConfigService */
        $Setting = $app['eccube.plugin.service.cpr.config'];

        $arrRedirectIds = array();
        $redirect_id    = '';
        $log_file       = $Setting->pluginPath . '/Log/redirect_product.log';

        // 商品IDチェック
        if (!empty($product_id)){
            $arrRedirectIds[] = $product_id;
        }else{
            return $redirect_id;
        }

        /* @var $ProductRepo \Eccube\Repository\ProductRepository */
        $ProductRepo = $app['eccube.repository.product'];
        while ((int)$product_id > 0){
            $qb = $ProductRepo->createQueryBuilder('p');
            $qb->select('pr.redirect_product_id')
                ->leftJoin('Plugin\ExcludeProductPayment\Entity\CprProductRedirect', 'pr', 'WITH', 'p.id = pr.id')
                ->andWhere('p.id               = :product_id')
                ->andWhere('p.Status           = 2')
                ->andWhere('p.del_flg          = 0')
                ->andWhere('pr.redirect_select = :redirect_select')
                ->setParameter('product_id'       , $product_id)
                ->setParameter('redirect_select' , $const['redirect_id'])
                ->setMaxResults(1);

            try {
                $arrRow = $qb->getQuery()->getSingleResult();
                $get_redirect_id = $arrRow['redirect_product_id'];
            } catch (\Doctrine\Orm\NoResultException $e) {
                $get_redirect_id = '';
            }

            // 抽出したリダイレクト先商品IDを検査
            if (!empty($get_redirect_id)){
                // 以前に抽出したものと同じであれば、空白にして返す
                if (in_array($get_redirect_id, $arrRedirectIds)){
                    $msg = 'リダイレクト先商品ID:' . $get_redirect_id . 'はループしています。リダイレクト順：' . implode(',', $arrRedirectIds);
                    $this->printLog($msg, $log_file);
                    $redirect_id = '';
                    break;
                }

                $arrRedirectIds[] = $get_redirect_id;
                $product_id       = $get_redirect_id;
                $redirect_id      = $get_redirect_id;
            }else{
                break;
            }
        }

        // 商品が存在するかどうかのチェック
        if ($redirect_id > 0){
            /* @var $Product \Eccube\Entity\Product */
            $Product = $app['eccube.repository.product']->find($redirect_id);
            $non_data  = empty($Product);

            if (!$non_data){
                $non_data  = $Product->getDelFlg() != 0;
            }

            if ($non_data){
                $msg  = 'リダイレクト先商品ID:' . $redirect_id . 'は';
                $msg .= '削除されているか存在しません。リダイレクト順：' . implode(',', $arrRedirectIds);
                $this->printLog($msg, $log_file);
                $redirect_id = '';
            }
        }

        return $redirect_id;
    }

    /**
     * ログのセット
     *
     * @param $message
     * @param array $context
     * @param int $level
     */
    public function printLog($message, $log_file, array $context = array(), $level = Logger::INFO)
    {
        $app = $this->app;

        $app->register(new \Silex\Provider\MonologServiceProvider(), array(
            'monolog.logfile' => $log_file,
        ));

        // ログ生成
        $app->log($message, $context, $level);

        // 元に戻す
        $app->initLogger();
    }

    /**
     * URLが内部か外部かの判定
     *
     * @param string $redirect_url
     * @return bool true:外部のURL false:内部のURL
     */
    public function checkInOutUrl($redirect_url){
        $isOpenGuide = false;
        $replace_pattern = '/^(http|https)/';
        $url =  preg_replace($replace_pattern, '', $this->app->url('homepage'), 1) ;

        $pattern = '/^(' . preg_quote('http' . $url, '/') . '|' . preg_quote('https' . $url, '/') . ')/';
        // アプリケーション外への直接のリダイレクトは扱わない
        if (preg_match($pattern, $redirect_url) === 0) {
            $isOpenGuide = true;
        }

        return $isOpenGuide;
    }
}
