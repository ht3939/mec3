<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20150706204400 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->createPluginTable($schema);
    }

    public function down(Schema $schema)
    {
        $schema->dropTable('plg_shoppingex');
    }

    protected function createPluginTable(Schema $schema)
    {
        $table = $schema->createTable("plg_shoppingex");
        $table->addColumn('order_id', 'integer');
        $table->addColumn('card1', 'text', array('notnull' => false));
        $table->addColumn('card2', 'text', array('notnull' => false));
        $table->addColumn('card3', 'text', array('notnull' => false));
        $table->addColumn('card4', 'text', array('notnull' => false));
        $table->addColumn('holder', 'text', array('notnull' => false));
        $table->addColumn('cardtype', 'integer', array('notnull' => false));
        $table->addColumn('limitmon', 'integer', array('notnull' => false));
        $table->addColumn('limityear', 'integer', array('notnull' => false));
        $table->addColumn('content', 'text', array('notnull' => false));
        $table->setPrimaryKey(array('order_id'));
    }
}
