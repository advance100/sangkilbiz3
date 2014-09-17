<?php

namespace core\inventory\models;

use Yii;

/**
 * This is the model class for table "stock_movement".
 *
 * @property integer $id_movement
 * @property string $movement_num
 * @property string $movement_date
 * @property integer $movement_type
 * @property integer $type_reff
 * @property integer $id_reff
 * @property string $description
 * @property integer $status
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property StockMovementDtl[] $stockMovementDtls
 */
class StockMovement extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stock_movement';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['movement_num', 'movement_date', 'movement_type', 'status', 'created_by', 'updated_by'], 'required'],
            [['movement_date', 'created_at', 'updated_at'], 'safe'],
            [['movement_type', 'type_reff', 'id_reff', 'status', 'created_by', 'updated_by'], 'integer'],
            [['movement_num'], 'string', 'max' => 16],
            [['description'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_movement' => 'Id Movement',
            'movement_num' => 'Movement Num',
            'movement_date' => 'Movement Date',
            'movement_type' => 'Movement Type',
            'type_reff' => 'Type Reff',
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
    public function getStockMovementDtls()
    {
        return $this->hasMany(StockMovementDtl::className(), ['id_movement' => 'id_movement']);
    }
}
