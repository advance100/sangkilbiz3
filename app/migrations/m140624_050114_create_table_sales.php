<?php

use yii\db\Schema;

class m140624_050114_create_table_sales extends \yii\db\Migration
{

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%sales}}', [
            'id_sales' => Schema::TYPE_PK,
            'sales_num' => Schema::TYPE_STRING . '(16) NOT NULL',
            'id_branch' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_customer' => Schema::TYPE_INTEGER,
            'sales_date' => Schema::TYPE_DATE . ' NOT NULL',
            'sales_value' => Schema::TYPE_FLOAT . ' NOT NULL',
            'discount' => Schema::TYPE_FLOAT . ' NULL',
            'status' => Schema::TYPE_INTEGER . ' NOT NULL',
            // history column
            'created_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            // constrain
            ], $tableOptions);

        $this->createTable('{{%sales_dtl}}', [
            'id_sales' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_product' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_uom' => Schema::TYPE_INTEGER . ' NOT NULL',
            'sales_qty' => Schema::TYPE_FLOAT . ' NOT NULL',
            'sales_price' => Schema::TYPE_FLOAT . ' NOT NULL',
            'sales_qty_release' => Schema::TYPE_FLOAT,
            'cogs' => Schema::TYPE_FLOAT . ' NOT NULL',
            'discount' => Schema::TYPE_FLOAT,
            'tax' => Schema::TYPE_FLOAT,
            // constrain
            'PRIMARY KEY (id_sales , id_product )',
            'FOREIGN KEY (id_sales) REFERENCES {{%sales}} (id_sales) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%sales_dtl}}');
        $this->dropTable('{{%sales}}');
    }
}