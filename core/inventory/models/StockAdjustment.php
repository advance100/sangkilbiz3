<?php

namespace core\inventory\models;

use Yii;

/**
 * This is the model class for table "stock_adjustment".
 *
 * @property integer $id_adjustment
 * @property string $adjustment_num
 * @property integer $id_warehouse
 * @property string $adjustment_date
 * @property integer $id_reff
 * @property string $description
 * @property integer $status
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property StockAdjustmentDtl[] $stockAdjustmentDtls
 */
class StockAdjustment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stock_adjustment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['adjustment_num', 'id_warehouse', 'adjustment_date', 'status', 'created_by', 'updated_by'], 'required'],
            [['id_warehouse', 'id_reff', 'status', 'created_by', 'updated_by'], 'integer'],
            [['adjustment_date', 'created_at', 'updated_at'], 'safe'],
            [['adjustment_num'], 'string', 'max' => 16],
            [['description'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_adjustment' => 'Id Adjustment',
            'adjustment_num' => 'Adjustment Num',
            'id_warehouse' => 'Id Warehouse',
            'adjustment_date' => 'Adjustment Date',
            'id_reff' => 'Id Reff',
            'description' => 'Description',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockAdjustmentDtls()
    {
        return $this->hasMany(StockAdjustmentDtl::className(), ['id_adjustment' => 'id_adjustment']);
    }
}
