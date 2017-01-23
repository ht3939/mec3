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

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version201507231300 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->createPlgSetProductPlugin($schema);
        // $this->createPlgSetProduct($schema);
        $this->createPlgProductSetProduct($schema);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $schema->dropTable('plg_setproduct_plugin');
        // $schema->dropTable('plg_setproduct');
        $schema->dropTable('plg_product_setproduct_maker');
    }

    protected function createPlgSetProductPlugin(Schema $schema)
    {
        $table = $schema->createTable("plg_setproduct_plugin");
        $table->addColumn('plugin_id', 'integer', array(
            'autoincrement' => true,
        ));

        $table->addColumn('plugin_code', 'text', array(
            'notnull' => true,
        ));

        $table->addColumn('plugin_name', 'text', array(
            'notnull' => true,
        ));

        $table->addColumn('sub_data', 'text', array(
            'notnull' => false,
        ));

        $table->addColumn('auto_update_flg', 'smallint', array(
            'notnull' => true,
            'unsigned' => false,
            'default' => 0,
        ));

        $table->addColumn('del_flg', 'smallint', array(
            'notnull' => true,
            'unsigned' => false,
            'default' => 0,
        ));

        $table->addColumn('create_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));

        $table->addColumn('update_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));

        $table->setPrimaryKey(array('plugin_id'));
    }

    // protected function createPlgSetProduct(Schema $schema)
    // {
    //     $table = $schema->createTable("plg_setproduct");
    //     $table->addColumn('setproduct_id', 'integer', array(
    //         'autoincrement' => true,
    //     ));

    //     $table->addColumn('name', 'text', array(
    //         'notnull' => true,
    //     ));

    //     $table->addColumn('rank', 'integer', array(
    //         'notnull' => true,
    //         'unsigned' => false,
    //         'default' => 0,
    //     ));

    //     $table->addColumn('del_flg', 'smallint', array(
    //         'notnull' => true,
    //         'unsigned' => false,
    //         'default' => 0,
    //     ));

    //     $table->addColumn('create_date', 'datetime', array(
    //         'notnull' => true,
    //         'unsigned' => false,
    //     ));

    //     $table->addColumn('update_date', 'datetime', array(
    //         'notnull' => true,
    //         'unsigned' => false,
    //     ));

    //     $table->setPrimaryKey(array('setproduct_id'));
    // }

    protected function createPlgProductSetProduct(Schema $schema)
    {
        $table = $schema->createTable("plg_product_setproduct_maker");
        $table->addColumn('product_id', 'integer', array(
            'notnull' => true,
        ));

        $table->addColumn('maker_id', 'text', array(
            'notnull' => true,
        ));

        $table->addColumn('setproduct_sim_flg', 'integer', array());

        $table->addColumn('del_flg', 'smallint', array(
            'notnull' => true,
            'unsigned' => false,
            'default' => 0,
        ));

        $table->addColumn('create_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));

        $table->addColumn('update_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));

        $table->setPrimaryKey(array('product_id'));
    }

    function getSetProductCode()
    {
        $config = \Eccube\Application::alias('config');

        return "";
    }
}
