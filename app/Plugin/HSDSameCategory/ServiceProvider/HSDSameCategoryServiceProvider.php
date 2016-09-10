<?php
namespace Plugin\HSDSameCategory\ServiceProvider;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Plugin\HSDSameCategory\Form\Type\HSDSameCategorySettingConfigType;

class HSDSameCategoryServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        //Repository
        $app['hsd_same_category_setting.repository.hsd_same_category_setting'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\HSDSameCategory\Entity\HSDSameCategorySetting');
        });

        $app->match('/' . $app['config']['admin_route'] . '/plugin/HSDSameCategory/config', 'Plugin\HSDSameCategory\Controller\ConfigController::index')->bind('plugin_HSDSameCategory_config');

        // Form
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new HSDSameCategorySettingConfigType($app);
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
        $app->match('/block/hsd_same_category', '\Plugin\HSDSameCategory\Controller\Block\HSDSameCategoryController::index')
            ->bind('block_hsd_same_category');

	}

    public function boot(BaseApplication $app)
    {
    }
}

?>
