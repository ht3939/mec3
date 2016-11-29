<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160520120000 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->createPluginTable($schema);
    }

    public function down(Schema $schema)
    {
        $schema->dropTable('plg_hsd_same_category_setting');
    }

    protected function createPluginTable(Schema $schema)
    {
        // セッティング
        $this->addSql('
            CREATE TABLE plg_hsd_same_category_setting (
              id varchar(32) NOT NULL,
              max_count integer NOT NULL,
              title varchar(128) NOT NULL,
              mode varchar(16) NOT NULL,
              show_type varchar(16) NOT NULL,
              pagination varchar(16) NOT NULL,
              navbuttons varchar(16) NOT NULL,
              showloop varchar(16) NOT NULL,
              PRIMARY KEY (id)
            )
        ');
    }

}

?>
