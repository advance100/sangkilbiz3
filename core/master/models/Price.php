<?php

namespace core\master\models;

use Yii;

/**
 * This is the model class for table "price".
 *
 * @property integer $id_product
 * @property integer $id_price_category
 * @property double $price
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property Product $idProduct
 * @property PriceCategory $idPriceCategory
 */
class Price extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'price';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_product', 'id_price_category', 'created_by', 'updated_by'], 'required'],
            [['id_product', 'id_price_category', 'created_by', 'updated_by'], 'integer'],
            [['price'], 'number'],
            [['created_at', 'updated_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_product' => 'Id Product',
            'id_price_category' => 'Id Price Category',
            'price' => 'Price',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdProduct()
    {
        return $this->hasOne(Product::className(), ['id_product' => 'id_product']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdPriceCategory()
    {
        return $this->hasOne(PriceCategory::className(), ['id_price_category' => 'id_price_category']);
    }
}
