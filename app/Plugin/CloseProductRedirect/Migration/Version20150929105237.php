<?php
/*
 * Copyright(c) 2015 SystemFriend Inc. All rights reserved.
 * http://ec-cube.systemfriend.co.jp/
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150929105237 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->createDtbCprPlugin($schema);
        $this->createDtbCprProductRedirect($schema);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->deletePageLayout();
        // this down() migration is auto-generated, please modify it to your needs
        $schema->dropTable('dtb_cpr_plugin');
        $schema->dropTable('dtb_cpr_product_redirect');

    }

    public function postUp(Schema $schema)
    {

        $app = new \Eccube\Application();
        $app->boot();
        $pluginCode = 'CloseProductRedirect';
        $pluginName = '非公開商品リダイレクト';
        $datetime = date('Y-m-d H:i:s');
        $insert = "INSERT INTO dtb_cpr_plugin(
                            plugin_code, plugin_name, create_date, update_date)
                    VALUES ('$pluginCode', '$pluginName', '$datetime', '$datetime'
                            );";
        $this->connection->executeUpdate($insert);
    }

    protected function createDtbCprPlugin(Schema $schema)
    {
        $table = $schema->createTable("dtb_cpr_plugin");
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

    protected function createDtbCprProductRedirect(Schema $schema)
    {
        $table = $schema->createTable("dtb_cpr_product_redirect");
        $table->addColumn('product_id', 'integer', array(
            'notnull' => true,
        ));
        $table->addColumn('redirect_product_id', 'integer', array(
            'notnull' => false,
        ));
        $table->addColumn('redirect_url', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('redirect_select', 'integer', array(
            'notnull' => false,
        ));

        $table->setPrimaryKey(array('product_id'));
    }

    protected function deletePagelayout()
    {
        $sql_delete = " DELETE FROM dtb_page_layout WHERE url = 'cpr_redirect_guide'";
        $this->connection->executeUpdate($sql_delete);

    }
}
