<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160308142743 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $table = $schema->createTable("plg_contact");
        $table->addColumn('contact_id', 'integer', array(
            'autoincrement' => true,
            'notnull' => true,
        ));
        $table->addColumn('name', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('kana', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('zip', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('addr', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('tel', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('email', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('contents', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('customer_id', 'integer', array(
            'notnull' => true,
            'unsigned' => false,
            'default' => 0,
        ));
        $table->addColumn('status', 'integer', array(
            'notnull' => true,
            'unsigned' => false,
            'default' => 1,
        ));
        $table->addColumn('comment', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('create_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));
        $table->addColumn('update_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));
        $table->setPrimaryKey(array('contact_id'));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $schema->dropTable('plg_contact');
        $schema->dropSequence('plg_contact_id_seq');

    }
}
