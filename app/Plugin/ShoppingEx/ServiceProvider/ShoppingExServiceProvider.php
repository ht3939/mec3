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
            ,'/simcard/uqmobile-onsei-3gb.php'=>'/products/detail/32'
            ,'/simcard/umobile-data-doublefix.php'=>'/products/detail/33'
            ,'/simcard/umobile-data-1gb.php'=>'/products/detail/34'
            ,'/simcard/umobile-data-5gb.php'=>'/products/detail/35'
            ,'/simcard/umobile-data-free.php'=>'/products/detail/36'
            ,'/simcard/umobile-onsei-doublefix.php'=>'/products/detail/37'
            ,'/simcard/umobile-onsei-3gb.php'=>'/products/detail/38'
            ,'/simcard/umobile-onsei-5gb.php'=>'/products/detail/39'
            ,'/simcard/umobile-onsei-free.php'=>'/products/detail/40'
            ,'/simcard/sonet-data-140mb.php'=>'/products/detail/41'
            ,'/simcard/sonet-data-200mb.php'=>'/products/detail/42'
            ,'/simcard/sonet-onsei-1gb.php'=>'/products/detail/86'
            ,'/simcard/sonet-onsei-free.php'=>'/products/detail/87'
            ,'/simcard/rakuten-onsei-3gb.php'=>'/products/detail/43'
            ,'/simcard/onlyservice-data-2gb.php'=>'/products/detail/44'
            ,'/simcard/ocn-onsei-3gb.php'=>'/products/detail/45'
            ,'/simcard/nifmo-onsei-3gb.php'=>'/products/detail/88'
            ,'/simcard/nifmo-onsei-5gb.php'=>'/products/detail/89'
            ,'/simcard/freetel-onsei-free.php'=>'/products/detail/46'
            ,'/simcard/dmm-onsei-7gb.php'=>'/products/detail/90'
            ,'/simcard/nifmo-data-3gb.php'=>'/products/detail/91'
            ,'/simcard/nifmo-data-5gb.php'=>'/products/detail/92'
            ,'/simcard/tjc-data-1gb.php'=>'/products/detail/47'
            ,'/simcard/tjc-data-3gb.php'=>'/products/detail/48'
            ,'/simcard/tjc-data-7gb.php'=>'/products/detail/85'
            ,'/simcard/ocn-data-110mb.php'=>'/products/detail/93'
            ,'/simcard/ocn-onsei-110mb.php'=>'/products/detail/50'
            ,'/simcard/ocn-onsei-5gb.php'=>'/products/detail/51'
            ,'/simcard/rakuten-data-basic.php'=>'/products/detail/52'
            ,'/simcard/rakuten-data-3gb.php'=>'/products/detail/53'
            ,'/simcard/rakuten-onsei-5gb.php'=>'/products/detail/54'
            ,'/simcard/rakuten-onsei-10gb.php'=>'/products/detail/55'
            ,'/simcard/ckks.php'=>'/products/detail/56'
            ,'/simcard/cggs.php'=>'/products/detail/57'
            ,'/simcard/uqmobile-speed.php'=>'/products/detail/58'
            ,'/simcard/uqmobile-limitless.php'=>'/products/detail/59'
            ,'/simcard/uqmobile-limitless-onsei.php'=>'/products/detail/60'
            ,'/simcard/biglobe-data-3gb-entry.php'=>'/products/detail/61'
            ,'/simcard/biglobe-data-6gb-light.php'=>'/products/detail/62'
            ,'/simcard/biglobe-data-12gb.php'=>'/products/detail/63'
            ,'/simcard/biglobe-data-sms-3gb-entry.php'=>'/products/detail/64'
            ,'/simcard/biglobe-data-sms-6gb-light.php'=>'/products/detail/65'
            ,'/simcard/biglobe-data-sms-12gb.php'=>'/products/detail/66'
            ,'/simcard/biglobe-onsei-sms-1gb-start.php'=>'/products/detail/67'
            ,'/simcard/biglobe-onsei-sms-3gb-entry.php'=>'/products/detail/68'
            ,'/simcard/biglobe-onsei-sms-6gb-light.php'=>'/products/detail/69'
            ,'/simcard/biglobe-onsei-sms-12gb.php'=>'/products/detail/70'
            ,'/simcard/iijmio-welcome-mini-d.php'=>'/products/detail/71'
            ,'/simcard/iijmio-welcome-light-d.php'=>'/products/detail/72'
            ,'/simcard/iijmio-welcome-family-d.php'=>'/products/detail/74'
            ,'/simcard/iijmio-onsei-mini-d.php'=>'/products/detail/75'
            ,'/simcard/iijmio-onsei-light-d.php'=>'/products/detail/76'
            ,'/simcard/iijmio-onsei-family-d.php'=>'/products/detail/77'
            ,'/simcard/iijmio-welcome-mini-a.php'=>'/products/detail/84'
            ,'/simcard/iijmio-welcome-light-a.php'=>'/products/detail/79'
            ,'/simcard/iijmio-welcome-family-a.php'=>'/products/detail/80'
            ,'/simcard/iijmio-onsei-mini-a.php'=>'/products/detail/81'
            ,'/simcard/iijmio-onsei-light-a.php'=>'/products/detail/82'
            ,'/simcard/iijmio-onsei-family-a.php'=>'/products/detail/83'
            );

        return $redirectto;

    }
}
