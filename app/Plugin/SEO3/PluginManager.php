<?php

/*
 * This file is part of the SEO3
 *
 * Copyright (C) 2016 Nobuhiko Kimoto
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\SEO3;

use Eccube\Plugin\AbstractPluginManager;

class PluginManager extends AbstractPluginManager
{
    /**
     * プラグインインストール時の処理
     *
     * @param $config
     * @param $app
     * @throws \Exception
     */
    public function install($config, $app)
    {
        $this->migrationSchema($app, __DIR__ . '/Resource/doctrine/migration', $config['code']);
    }

    /**
     * プラグイン削除時の処理
     *
     * @param $config
     * @param $app
     */
    public function uninstall($config, $app)
    {
        $this->migrationSchema($app, __DIR__ . '/Resource/doctrine/migration', $config['code'], 0);
    }

    /**
     * プラグイン有効時の処理
     *
     * @param $config
     * @param $app
     * @throws \Exception
     */
    public function enable($config, $app)
    {
    }

    /**
     * プラグイン無効時の処理
     *
     * @param $config
     * @param $app
     */
    public function disable($config, $app)
    {
    }


    public function update($config, $app)
    {
        $this->migrationSchema($app, __DIR__ . '/Resource/doctrine/update', $config['code']);
    }
}
