<?php

namespace DoctrineMigrations;

use Plugin\TagEx\Migration\MigrationSupport;
use Doctrine\DBAL\Schema\Schema;
use Plugin\TagEx\Entity\TagEx;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160515114059 extends MigrationSupport
{

    private function initTable()
    {
        $this->createTables = array(
                'plg_tag_ex' => array(
                        array('tag_ex_id', 'integer', array('autoincrement' => true), true),
                        array('tag_id', 'integer', array('notnull' => true, 'unsigned' => false)),
                        array('color1', 'text', array('notnull' => false)),
                        array('color2', 'text', array('notnull' => false)),
                        array('color3', 'text', array('notnull' => false)),
                ),
        );

        $this->updateTables = array();
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->initTable();
        parent::up($schema);
    }

    /**
     * @param Schema $schema
     */
    public function postUp(Schema $schema) {

        // 初期値生成

        $app = new \Eccube\Application();
        $app->boot();

        $select = "select id, name, rank from mtb_tag order by rank";
        $arrTag = $this->connection->executeQuery($select)->fetchAll();

        $insert = "insert into plg_tag_ex ";
        if($app['config']['database']['driver'] == 'pdo_pgsql') {
            $insert .= "(tag_ex_id, tag_id)";
            $insert .= "values (";
            $insert .= " nextval('plg_tag_ex_tag_ex_id_seq'), :tag_id);";
        } else {
            $insert .= "(tag_id)";
            $insert .= "values (";
            $insert .= " :tag_id);";
        }

        $count = 1;
        foreach ($arrTag as $tag) {

            $arrValue = array(
                    'tag_id' => $tag['id'],
            );

            $this->connection->executeUpdate($insert, $arrValue);

            $count++;
        }

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->initTable();
        parent::down($schema);
    }
}
