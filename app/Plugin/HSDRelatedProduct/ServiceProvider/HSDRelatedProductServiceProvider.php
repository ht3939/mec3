<?php
namespace Plugin\HSDRelatedProduct\ServiceProvider;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Plugin\HSDRelatedProduct\Form\Type\HSDRelatedProductSettingConfigType;

class HSDRelatedProductServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        //Repository
        $app['hsd_related_product.repository.hsd_related_product'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\HSDRelatedProduct\Entity\HSDRelatedProduct');
        });
        $app['hsd_related_product_setting.repository.hsd_related_product_setting'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\HSDRelatedProduct\Entity\HSDRelatedProductSetting');
        });

        $app->match('/' . $app['config']['admin_route'] . '/plugin/HSDRelatedProduct/config', 'Plugin\HSDRelatedProduct\Controller\ConfigController::index')->bind('plugin_HSDRelatedProduct_config');

        // Form
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new HSDRelatedProductSettingConfigType($app);
            return $types;
        }));

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
        $app->match('/block/hsd_related_product', '\Plugin\HSDRelatedProduct\Controller\Block\HSDRelatedProductController::index')
            ->bind('block_hsd_related_product');

	}

    public function boot(BaseApplication $app)
    {
    }
}

?>
