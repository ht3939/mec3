<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version201510211300 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->createABTestCfgProduct($schema);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $schema->dropTable('plg_abtestcfg_product');
        $schema->dropSequence('plg_abtestcfg_product_abtestcfg_product_id_seq');
    }

    /**
     * おすすめ商品テーブル作成
     * @param Schema $schema
     */
    protected function createABTestCfgProduct(Schema $schema)
    {
        $table = $schema->createTable("plg_abtestcfg_product");
        $table->addColumn('abtestcfg_product_id', 'integer', array(
            'autoincrement' => true,
            'notnull' => true,
        ));
        $table->addColumn('abtestidentity', 'text', array(
            'notnull' => true,
        ));
        $table->addColumn('enable_flg', 'smallint', array(
            'notnull' => true,
        ));
        $table->addColumn('headtags', 'text', array(
            'notnull' => false,
        ));
        $table->addColumn('tagdevice', 'text', array(
            'notnull' => true,
        ));
        $table->addColumn('conditions', 'text', array(
            'notnull' => true,
        ));
        $table->addColumn('tagurl', 'text', array(
            'notnull' => true,
        ));
        $table->addColumn('abrule', 'text', array(
            'notnull' => true,
        ));

        $table->addColumn('aburl', 'text', array(
            'notnull' => false,
        ));

        $table->addColumn('organic_flg', 'integer', array(
            'notnull' => true,
            'default' => 0,
        ));

        $table->addColumn('rank', 'integer', array(
            'notnull' => true,
            'unsigned' => false,
            'default' => 1,
        ));

        $table->addColumn('del_flg', 'smallint', array(
            'notnull' => true,
            'unsigned' => false,
            'default' => 0,
        ));

        $table->addColumn('create_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));

        $table->addColumn('update_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));

        $table->setPrimaryKey(array('abtestcfg_product_id'));
    }

}
