<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version201702132044 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->updatePlgProductOptionExtension($schema);
    }

    public function down(Schema $schema)
    {
        // $this->dropPlgProductOptionExtension($schema);
    }

    protected function updatePlgProductOptionExtension(Schema $schema)
    {
        $table = $schema->getTable("plg_productoption_dtb_extension");
        if($table){
            $table->addColumn('exclude_payment_flg', 'smallint', array(
                'notnull' => false,
                'default' => 0,
            ));

        }
    }

    protected function dropPlgProductOptionExtension(Schema $schema)
    {
        // $table = $schema->getTable("plg_productoption_dtb_extension");
        // if($table){
        //     $table->removeColumn('exclude_payment_flg');

        // }
    }

}