<?php

namespace core\master\models;

use Yii;

/**
 * This is the model class for table "product_stock".
 *
 * @property integer $id_warehouse
 * @property integer $id_product
 * @property integer $qty_stock
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property Warehouse $idWarehouse
 * @property Product $idProduct
 */
class ProductStock extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_stock';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_warehouse', 'id_product', 'qty_stock', 'created_by', 'updated_by'], 'required'],
            [['id_warehouse', 'id_product', 'qty_stock', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_warehouse' => 'Id Warehouse',
            'id_product' => 'Id Product',
            'qty_stock' => 'Qty Stock',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdWarehouse()
    {
        return $this->hasOne(Warehouse::className(), ['id_warehouse' => 'id_warehouse']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdProduct()
    {
        return $this->hasOne(Product::className(), ['id_product' => 'id_product']);
    }
}
