<?php
namespace Plugin\MakerRelatedProduct\ServiceProvider;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Plugin\MakerRelatedProduct\Form\Type\MakerRelatedProductSettingConfigType;

class MakerRelatedProductServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        //Repository
        $app['maker_related_product.repository.maker_related_product'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\MakerRelatedProduct\Entity\MakerRelatedProduct');
        });
        $app['maker_related_product_setting.repository.maker_related_product_setting'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\MakerRelatedProduct\Entity\MakerRelatedProductSetting');
        });

        $app->match('/' . $app['config']['admin_route'] . '/plugin/HSDRelatedProduct/config', 'Plugin\MakerRelatedProduct\Controller\ConfigController::index')->bind('plugin_MakerRelatedProduct_config');

        // Form
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new MakerRelatedProductSettingConfigType($app);
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
        $app->match('/block/maker_related_product', '\Plugin\MakerRelatedProduct\Controller\Block\MakerRelatedProductController::index')
            ->bind('block_maker_related_product');

	}

    public function boot(BaseApplication $app)
    {
    }
}

?>
