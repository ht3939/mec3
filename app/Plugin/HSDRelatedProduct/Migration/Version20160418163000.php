<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160418163000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->createPluginTable($schema);
    }

    public function down(Schema $schema)
    {
        $schema->dropTable('plg_hsd_related_product');
        $schema->dropTable('plg_hsd_related_product_setting');
    }

    protected function createPluginTable(Schema $schema)
    {
        // データ保存用
        $this->addSql('
            CREATE TABLE plg_hsd_related_product (
              id varchar(32) NOT NULL,
              from_id varchar(32) NOT NULL,
              to_id varchar(32) NOT NULL,
              updated_at timestamp NOT NULL,
              PRIMARY KEY (id)
            )
        ');

        // セッティング
        $this->addSql('
            CREATE TABLE plg_hsd_related_product_setting (
              id varchar(32) NOT NULL,
              max_num integer NOT NULL,
              max_row_num integer NOT NULL,
              title varchar(128) NOT NULL,
              show_price varchar(16) NOT NULL,
              PRIMARY KEY (id)
            )
        ');

    }

}

?>
