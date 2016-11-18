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
        $schema->dropTable('plg_shoppingex_cleanup');
    }

    protected function createPluginTable(Schema $schema)
    {
        $table = $schema->createTable("plg_shoppingex");
        $table->addColumn('order_id', 'integer');
        $table->addColumn('cardno1', 'text', array('notnull' => false));
        $table->addColumn('cardno2', 'text', array('notnull' => false));
        $table->addColumn('cardno3', 'text', array('notnull' => false));
        $table->addColumn('cardno4', 'text', array('notnull' => false));
        $table->addColumn('holder', 'text', array('notnull' => false));
        $table->addColumn('cardtype', 'integer', array('notnull' => false));
        $table->addColumn('limitmon', 'integer', array('notnull' => false));
        $table->addColumn('limityear', 'integer', array('notnull' => false));
        $table->addColumn('cardsec', 'text', array('notnull' => false));
        $table->addColumn('content', 'text', array('notnull' => false));
        $table->setPrimaryKey(array('order_id'));

        $table = $schema->createTable("plg_shoppingex_cleanup");
        $table->addColumn('order_id', 'integer');
        $table->addColumn('create_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));

        $table->setPrimaryKey(array('order_id'));

    }
}
