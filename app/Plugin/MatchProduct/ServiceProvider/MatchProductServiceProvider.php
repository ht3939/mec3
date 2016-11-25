<?php
namespace Plugin\MatchProduct\ServiceProvider;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Plugin\MatchProduct\Form\Type\MatchProductSettingConfigType;

class MatchProductServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        //Repository
        // $app['maker_related_product.repository.maker_related_product'] = $app->share(function () use ($app) {
        //     return $app['orm.em']->getRepository('Plugin\MatchProduct\Entity\MatchProduct');
        // });
        // $app['maker_related_product_setting.repository.maker_related_product_setting'] = $app->share(function () use ($app) {
        //     return $app['orm.em']->getRepository('Plugin\MatchProduct\Entity\MatchProductSetting');
        // });

        // $app->match('/' . $app['config']['admin_route'] . '/plugin/MatchProduct/config', 'Plugin\MatchProduct\Controller\ConfigController::index')->bind('plugin_MatchProduct_config');

        // Form
        // $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
        //     $types[] = new MatchProductSettingConfigType($app);
        //     return $types;
        // }));

        // Message
        $app['translator'] = $app->share($app->extend('translator', function ($translator, \Silex\Application $app) {
            $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
            $file = __DIR__ . '/../Resource/locale/message.' . $app['locale'] . '.yml';
            if (file_exists($file)) {
                $translator->addResource('yaml', $file, $app['locale']);
            }
            return $translator;
        }));

        // ブロックのルーティング設定。ルーティング名の接頭辞にblock_を付ける
        $app->match('/block/match_product', '\Plugin\MatchProduct\Controller\Block\MatchProductController::index')
            ->bind('block_match_product');

	}

    public function boot(BaseApplication $app)
    {
    }
}

?>
