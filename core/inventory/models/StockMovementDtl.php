<?php

namespace core\inventory\models;

use Yii;

/**
 * This is the model class for table "stock_movement_dtl".
 *
 * @property integer $id_movement
 * @property integer $id_warehouse
 * @property integer $id_product
 * @property double $qty
 * @property double $item_value Use to change cogs item.
 * When `null` mean no change for cogs.
 * @property double $trans_value Transaction value acording with this item.
 * Value can be purchase price or sales price.
 *
 * @property StockMovement $stockMovement
 */
class StockMovementDtl extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%stock_movement_dtl}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_movement', 'id_warehouse', 'id_product', 'qty'], 'required'],
            [['id_movement', 'id_warehouse', 'id_product'], 'integer'],
            [['qty', 'item_value'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_movement' => 'Id Movement',
            'id_warehouse' => 'Id Warehouse',
            'id_product' => 'Id Product',
            'qty' => 'Qty',
            'item_value' => 'Item Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockMovement()
    {
        return $this->hasOne(StockMovement::className(), ['id_movement' => 'id_movement']);
    }
}
