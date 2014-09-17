<?php

namespace core\sales\models;

use Yii;

/**
 * This is the model class for table "sales_dtl".
 *
 * @property integer $id_sales
 * @property integer $id_product
 * @property integer $id_uom
 * @property double $sales_qty
 * @property double $sales_price
 * @property double $cogs
 * @property double $discount
 * @property double $tax
 *
 * @property Sales $idSales
 */
class SalesDtl extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sales_dtl';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_sales', 'id_product', 'id_uom', 'sales_qty', 'sales_price', 'cogs'], 'required'],
            [['id_sales', 'id_product', 'id_uom'], 'integer'],
            [['sales_qty', 'sales_price', 'cogs', 'discount', 'tax'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_sales' => 'Id Sales',
            'id_product' => 'Id Product',
            'id_uom' => 'Id Uom',
            'sales_qty' => 'Sales Qty',
            'sales_price' => 'Sales Price',
            'cogs' => 'Cogs',
            'discount' => 'Discount',
            'tax' => 'Tax',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdSales()
    {
        return $this->hasOne(Sales::className(), ['id_sales' => 'id_sales']);
    }
}
