<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20161124204400 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->createPluginTable($schema);
    }

    public function down(Schema $schema)
    {
        $schema->dropTable('plg_shoppingex_cleanup');
    }

    protected function createPluginTable(Schema $schema)
    {

        $table = $schema->createTable("plg_shoppingex_cleanup");
        $table->addColumn('order_id', 'integer');
        $table->addColumn('create_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));

        $table->setPrimaryKey(array('order_id'));

    }
}
