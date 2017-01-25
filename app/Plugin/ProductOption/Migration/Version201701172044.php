<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version201701172044 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->createPlgProductOptionExtension($schema);
    }

    public function down(Schema $schema)
    {
        $schema->dropTable('plg_productoption_dtb_extension');
    }

    protected function createPlgProductOptionExtension(Schema $schema)
    {
        $table = $schema->createTable("plg_productoption_dtb_extension");
        $table->addColumn('option_id', 'integer', array(
            'notnull' => true,
        ));
        $table->addColumn('descdisp_flg', 'smallint', array(
            'notnull' => false,
            'default' => 0,
        ));
        $table->setPrimaryKey(array('option_id'));
    }


}