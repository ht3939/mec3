<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160318154347 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $table = $schema->createTable("plg_seo");
        $table->addColumn('id', 'integer', array(
            'autoincrement' => true,
            'notnull' => true,
        ));
        $table->addColumn('product_id', 'integer', array(
            'notnull' => false,
            'unsigned' => false,
        ));
        $table->addColumn('category_id', 'integer', array(
            'notnull' => false,
            'unsigned' => false,
        ));
        $table->addColumn('description', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('keywords', 'text', array(
            'notnull' => false,
        ));
        $table->setPrimaryKey(array('id'));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $schema->dropTable('plg_seo');
        $schema->dropSequence('plg_seo_id_seq');
    }
}
