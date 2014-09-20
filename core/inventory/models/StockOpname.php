<?php

namespace core\inventory\models;

use Yii;

/**
 * This is the model class for table "stock_opname".
 *
 * @property integer $id_opname
 * @property string $opname_num
 * @property integer $id_warehouse
 * @property string $opname_date
 * @property integer $status
 * @property string $description
 * @property string $operator
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property StockOpnameDtl[] $stockOpnameDtls
 */
class StockOpname extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%stock_opname}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['opname_num', 'id_warehouse', 'opname_date', 'status', 'created_by', 'updated_by'], 'required'],
            [['id_warehouse', 'status', 'created_by', 'updated_by'], 'integer'],
            [['opname_date', 'created_at', 'updated_at'], 'safe'],
            [['opname_num'], 'string', 'max' => 16],
            [['description', 'operator'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_opname' => 'Id Opname',
            'opname_num' => 'Opname Num',
            'id_warehouse' => 'Id Warehouse',
            'opname_date' => 'Opname Date',
            'status' => 'Status',
            'description' => 'Description',
            'operator' => 'Operator',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockOpnameDtls()
    {
        return $this->hasMany(StockOpnameDtl::className(), ['id_opname' => 'id_opname']);
    }
}
