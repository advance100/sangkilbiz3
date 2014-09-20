<?php

use yii\db\Schema;

class m140624_050030_create_table_purchase extends \yii\db\Migration
{

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%purchase}}', [
            'id_purchase' => Schema::TYPE_PK,
            'purchase_num' => Schema::TYPE_STRING . '(16) NOT NULL',
            'id_supplier' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_branch' => Schema::TYPE_INTEGER . ' NOT NULL',
            'purchase_date' => Schema::TYPE_DATE . ' NOT NULL',
            'purchase_value' => Schema::TYPE_FLOAT . ' NOT NULL',
            'discount' => Schema::TYPE_FLOAT,
            'status' => Schema::TYPE_INTEGER . ' NOT NULL',
            // history column
            'created_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);

        $this->createTable('{{%purchase_dtl}}', [
            'id_purchase' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_product' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_uom' => Schema::TYPE_INTEGER . ' NOT NULL',
            'purch_qty' => Schema::TYPE_FLOAT . ' NOT NULL',
            'purch_price' => Schema::TYPE_FLOAT . ' NOT NULL',
            'discount' => Schema::TYPE_FLOAT,
            'purch_qty_receive' => Schema::TYPE_FLOAT,
            // constrain
            'PRIMARY KEY (id_purchase , id_product)',
            'FOREIGN KEY (id_purchase) REFERENCES {{%purchase}} (id_purchase) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);

    }

    public function safeDown()
    {
        $this->dropTable('{{%purchase_dtl}}');
        $this->dropTable('{{%purchase}}');
    }
}
