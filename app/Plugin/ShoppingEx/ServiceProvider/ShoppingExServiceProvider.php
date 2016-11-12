<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
* http://www.lockon.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\ShoppingEx\ServiceProvider;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class ShoppingExServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        //$c->match('/help/tradelaw', '\Eccube\Controller\HelpController::tradelaw')->bind('help_tradelaw');
        $app->match('/help/company' , '\Eccube\Controller\HelpController::tradelaw')->bind('help_tradelaw');
        $app->match('/help/about' , 'Plugin\ShoppingEx\Controller\RedirectController::index')->bind('help_about');
        $app->match('/help/guide' , 'Plugin\ShoppingEx\Controller\RedirectController::index')->bind('help_guide_404');

        $app->match('/about-sim/' , 'Plugin\ShoppingEx\Controller\AboutSimController::index')->bind('about-sim');
        //$c->match('/about-sim', '\Eccube\Controller\UserDataController::index')->bind('aboutsim');

        $app->match('/guide' , '\Eccube\Controller\HelpController::guide')->bind('help_guide');

        // ブロック
        $app->match('/block/important_matter_block', '\Plugin\ShoppingEx\Controller\Block\ImportantMatterController::index')
            ->bind('block_important_matter_block');

        foreach($this->getRedirectTo() as $k=>$v){
            $app->match($k , 'Plugin\ShoppingEx\Controller\RedirectController::index');

        }

        // Form/Type
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use($app) {
            $types[] = new \Plugin\ShoppingEx\Form\Type\CardNoType($app['config']);
            $types[] = new \Plugin\ShoppingEx\Form\Type\CardLimitType($app['config']);
            $types[] = new \Plugin\ShoppingEx\Form\Type\CardTypeType($app['config']);
            $types[] = new \Plugin\ShoppingEx\Form\Type\CardSecType($app['config']);
            $types[] = new \Plugin\ShoppingEx\Form\Type\CardFormType($app);
            return $types;
        }));


        // Form/Extension
        $app['form.type.extensions'] = $app->share($app->extend('form.type.extensions', function ($extensions) {
            $extensions[] = new \Plugin\ShoppingEx\Form\Extension\ShoppingExExtension();
            return $extensions;
        }));

        //Repository
        $app['shoppingex.repository.shoppingex'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\ShoppingEx\Entity\ShoppingEx');
        });

        // サービスの登録
        $app['eccube.plugin.shoppingex.service.shoppingex'] = $app->share(function () use ($app) {
            return new \Plugin\ShoppingEx\Service\ShoppingExService($app);
        });

        $app['eccube.plugin.shoppingex.service.shoppingex']->setRedirectTo($this->getRedirectTo());
    }

    public function boot(BaseApplication $app)
    {
    }
    public function getRedirectTo(){
        $redirectto = array(
            '/guide.php'=>'/guide'
            ,'/company.php'=>'/help/company'
            ,'/privacy.php'=>'/help/privacy'
            ,'/simfree-sumaho/'=>'/products/list?category_id=2'
            ,'/simcard/'=>'/products/list?category_id=1'
            ,'/simfree-sumaho/iphone6.php'=>'/products/detail/94'
            ,'/simfree-sumaho/iphone5.php'=>'/products/detail/95'
            ,'/simfree-sumaho/zenfone2.php'=>'/products/detail/11'
            ,'/simfree-sumaho/xm.php'=>'/products/detail/96'
            ,'/simfree-sumaho/priori2.php'=>'/products/detail/97'
            ,'/simfree-sumaho/priori2_lte.php'=>'/products/detail/98'
            ,'/simfree-sumaho/nico.php'=>'/products/detail/99'
            ,'/simfree-sumaho/nexus6.php'=>'/products/detail/12'
            ,'/simfree-sumaho/ascend-g6.php'=>'/products/detail/13'
            ,'/simfree-sumaho/ascend-mate7.php'=>'/products/detail/14'
            ,'/simfree-sumaho/ascend-g620s.php'=>'/products/detail/100'
            ,'/simfree-sumaho/ascend-p8lite.php'=>'/products/detail/15'
            ,'/simfree-sumaho/torque.php'=>'/products/detail/101'
            ,'/simfree-sumaho/kc01.php'=>'/products/detail/16'
            ,'/simfree-sumaho/galaxy-s5.php'=>'/products/detail/17'
            ,'/simfree-sumaho/xperia-z3.php'=>'/products/detail/18'
            ,'/simfree-sumaho/xperia-z3-compact.php'=>'/products/detail/102'
            ,'/simfree-sumaho/blade-vec.php'=>'/products/detail/103'
            ,'/simfree-sumaho/g02.php'=>'/products/detail/104'
            ,'/simfree-sumaho/xperia-z4.php'=>'/products/detail/105'
            ,'/simfree-sumaho/arrows-m02.php'=>'/products/detail/19'
            ,'/simfree-sumaho/rei.php'=>'/products/detail/20'
            ,'/simfree-sumaho/arrows-m03.php'=>'/products/detail/21'
            ,'/simfree-sumaho/huawei-p9.php'=>'/products/detail/22'
            ,'/simfree-sumaho/huawei-p9lite.php'=>'/products/detail/23'
            ,'/simfree-sumaho/zenfone-go.php'=>'/products/detail/24'
            ,'/simfree-sumaho/zte-blade-e01.php'=>'/products/detail/25'
            ,'/simfree-sumaho/zte-blade-v580.php'=>'/products/detail/26'
            ,'/simfree-sumaho/zenfone-max.php'=>'/products/detail/27'
            ,'/simfree-sumaho/zenfone3.php'=>'/products/detail/28'
            ,'/simfree-sumaho/zenfone2-laser.php'=>'/products/detail/29'
            ,'/simfree-sumaho/zte-blade-v7lite.php'=>'/products/detail/30'
            ,'/simfree-sumaho/zte-blade-v7max.php'=>'/products/detail/31'
            );

        return $redirectto;

    }
}
