<?php

use yii\db\Schema;

class m140624_050056_create_table_inventory extends \yii\db\Migration
{

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%transfer}}', [
            'id_transfer' => Schema::TYPE_PK,
            'transfer_num' => Schema::TYPE_STRING . '(16) NOT NULL',
            'id_branch' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_branch_dest' => Schema::TYPE_INTEGER . ' NOT NULL',
            'transfer_date' => Schema::TYPE_DATE . ' NOT NULL',
            'status' => Schema::TYPE_INTEGER . ' NOT NULL',
            // history column
            'created_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            ], $tableOptions);

        $this->createTable('{{%transfer_dtl}}', [
            'id_transfer' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_product' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_uom' => Schema::TYPE_INTEGER . ' NOT NULL',
            'transfer_qty' => Schema::TYPE_FLOAT,
            'transfer_qty_send' => Schema::TYPE_FLOAT,
            'transfer_qty_receive' => Schema::TYPE_FLOAT,
            // constrain
            'PRIMARY KEY (id_transfer , id_product )',
            'FOREIGN KEY (id_transfer) REFERENCES {{%transfer}} (id_transfer) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);

        $this->createTable('{{%stock_opname}}', [
            'id_opname' => Schema::TYPE_PK,
            'opname_num' => Schema::TYPE_STRING . '(16) NOT NULL',
            'id_warehouse' => Schema::TYPE_INTEGER . ' NOT NULL',
            'opname_date' => Schema::TYPE_DATE . ' NOT NULL',
            'status' => Schema::TYPE_INTEGER . ' NOT NULL',
            'description' => Schema::TYPE_STRING,
            'operator' => Schema::TYPE_STRING,
            // history column
            'created_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            ], $tableOptions);

        $this->createTable('{{%stock_opname_dtl}}', [
            'id_opname' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_product' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_uom' => Schema::TYPE_INTEGER . ' NOT NULL',
            'qty' => Schema::TYPE_FLOAT . ' NOT NULL',
            // constrain
            'PRIMARY KEY (id_opname , id_product)',
            'FOREIGN KEY (id_opname) REFERENCES {{%stock_opname}} (id_opname) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);

        $this->createTable('{{%stock_adjustment}}', [
            'id_adjustment' => Schema::TYPE_PK,
            'adjustment_num' => Schema::TYPE_STRING . '(16) NOT NULL',
            'id_warehouse' => Schema::TYPE_INTEGER . ' NOT NULL',
            'adjustment_date' => Schema::TYPE_DATE . ' NOT NULL',
            'id_reff' => Schema::TYPE_INTEGER,
            'description' => Schema::TYPE_STRING,
            'status' => Schema::TYPE_INTEGER . ' NOT NULL',
            // history column
            'created_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            ], $tableOptions);

        $this->createTable('{{%stock_adjustment_dtl}}', [
            'id_adjustment' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_product' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_uom' => Schema::TYPE_INTEGER . ' NOT NULL',
            'qty' => Schema::TYPE_FLOAT . ' NOT NULL',
            'item_value' => Schema::TYPE_FLOAT . ' NOT NULL',
            // constrain
            'PRIMARY KEY (id_adjustment , id_product)',
            'FOREIGN KEY (id_adjustment) REFERENCES {{%stock_adjustment}} (id_adjustment) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);

        $this->createTable('{{%good_movement}}', [
            'id_movement' => Schema::TYPE_PK,
            'movement_num' => Schema::TYPE_STRING . '(16) NOT NULL',
            'movement_date' => Schema::TYPE_DATE . ' NOT NULL',
            'movement_type' => Schema::TYPE_INTEGER . ' NOT NULL',
            'type_reff' => Schema::TYPE_INTEGER,
            'id_reff' => Schema::TYPE_INTEGER,
            'description' => Schema::TYPE_STRING,
            'status' => Schema::TYPE_INTEGER . ' NOT NULL',
            // history column
            'created_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
            'updated_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            ], $tableOptions);

        $this->createTable('{{%good_movement_dtl}}', [
            'id_movement' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_warehouse' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_product' => Schema::TYPE_INTEGER . ' NOT NULL',
            'qty' => Schema::TYPE_FLOAT . ' NOT NULL',
            'item_value' => Schema::TYPE_FLOAT,
            'trans_value' => Schema::TYPE_FLOAT,
            // constrain
            'PRIMARY KEY (id_movement , id_warehouse, id_product)',
            'FOREIGN KEY (id_movement) REFERENCES {{%good_movement}} (id_movement) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);

        $this->createTable('{{%product_stock_history}}', [
            'stock_time' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
            'id_warehouse' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_product' => Schema::TYPE_INTEGER . ' NOT NULL',
            'qty_stock' => Schema::TYPE_INTEGER . ' NOT NULL',
            // constrain
            'PRIMARY KEY (stock_time, id_warehouse , id_product )',
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%product_stock_history}}');
        
        $this->dropTable('{{%stock_movement_dtl}}');
        $this->dropTable('{{%stock_movement}}');
        
        $this->dropTable('{{%stock_adjustment_dtl}}');
        $this->dropTable('{{%stock_adjustment}}');

        $this->dropTable('{{%stock_opname_dtl}}');
        $this->dropTable('{{%stock_opname}}');

        $this->dropTable('{{%transfer_dtl}}');
        $this->dropTable('{{%transfer}}');
    }
}
