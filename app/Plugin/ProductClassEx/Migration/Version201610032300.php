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
class Version201610032300 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->createPlgProductClassExPlugin($schema);

        // create table  Plug-in
        $this->createPlgProductClassEx($schema);

        $this->createPlgProductClassExImage($schema);

        // create Sequence  Plug-in
        $this->createPlgProductClassExProductClassExIdSeq($schema);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $schema->dropTable('plg_product_classex_plugin');
        $schema->dropTable('plg_product_classex_image');
        $schema->dropTable('plg_product_classex');

        // drop sequence.
        $schema->dropSequence('plg_product_classex_product_classex_id_seq');
    }

    protected function createPlgProductClassExPlugin(Schema $schema)
    {
        $table = $schema->createTable("plg_product_classex_plugin");
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

    /***
     * plg_product_classex テーブルの作成
     * @param Schema $schema
     */
    protected function createPlgProductClassEx(Schema $schema)
    {
        $table = $schema->createTable("plg_product_classex");
        $table->addColumn('product_classex_id', 'integer', array(
            'autoincrement' => true,
        ));

        $table->addColumn('product_id', 'integer', array(
            'notnull' => true,
        ));
        $table->addColumn('product_type_id', 'smallint', array(
            'notnull' => true,
        ));
        $table->addColumn('class_category_id1', 'integer', array(
            'notnull' => false,
        ));
        $table->addColumn('class_category_id2', 'integer', array(
            'notnull' => false,
        ));
        $table->addColumn('delivery_date_id', 'integer', array(
            'notnull' => false,
        ));
        $table->addColumn('creator_id', 'integer', array(
            'notnull' => true,
        ));
        $table->addColumn('product_code', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('stock', 'decimal', array(
            'notnull' => false,
            'precision'=>10,
            'scale'=>0,
        ));
        $table->addColumn('stock_unlimited', 'smallint', array(
            'notnull' => true,
        ));
        $table->addColumn('sale_limit', 'decimal', array(
            'notnull' => false,
            'precision'=>10,
            'scale'=>0,
        ));
        $table->addColumn('price01', 'decimal', array(
            'notnull' => false,
            'precision'=>10,
            'scale'=>0,
        ));
        $table->addColumn('price02', 'decimal', array(
            'notnull' => false,
            'precision'=>10,
            'scale'=>0,
        ));
        $table->addColumn('delivery_fee', 'decimal', array(
            'notnull' => false,
            'precision'=>10,
            'scale'=>0,
        ));

        $table->addColumn('create_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));

        $table->addColumn('update_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));

        $table->addColumn('del_flg', 'smallint', array(
            'notnull' => true,
            'unsigned' => false,
            'default' => 0,
        ));

        $table->setPrimaryKey(array('product_classex_id'));
    }

    /***
     * plg_product_classex_image テーブルの作成
     * @param Schema $schema
     */
    protected function createPlgProductClassExImage(Schema $schema)
    {
        $table = $schema->createTable("plg_product_classex_image");
        $table->addColumn('product_classex_image_id', 'integer', array(
            'autoincrement' => true,
        ));
        $table->addColumn('product_classex_id', 'integer', array(
            'notnull' => true,
        ));
        $table->addColumn('file_name', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('rank', 'integer', array(
            'notnull' => true,
        ));
        $table->addColumn('creator_id', 'integer', array(
            'notnull' => true,
        ));

        $table->addColumn('create_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));


        $table->setPrimaryKey(array('product_classex_image_id'));
    }

    /**
     * plg_send_history_send_id_seqの作成
     * @param Schema $schema
     */
    protected function createPlgProductClassExProductClassExIdSeq(Schema $schema) {
        $seq = $schema->createSequence("plg_product_classex_product_classex_id_seq");
    }


    function getProductClassExCode()
    {
        $config = \Eccube\Application::alias('config');

        return "";
    }
}