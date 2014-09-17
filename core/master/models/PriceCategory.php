<?php

namespace core\master\models;

use Yii;

/**
 * This is the model class for table "price_category".
 *
 * @property integer $id_price_category
 * @property string $nm_price_category
 * @property string $formula
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property Price[] $prices
 * @property Product[] $idProducts
 */
class PriceCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'price_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nm_price_category', 'created_by', 'updated_by'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['nm_price_category'], 'string', 'max' => 64],
            [['formula'], 'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_price_category' => 'Id Price Category',
            'nm_price_category' => 'Nm Price Category',
            'formula' => 'Formula',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrices()
    {
        return $this->hasMany(Price::className(), ['id_price_category' => 'id_price_category']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdProducts()
    {
        return $this->hasMany(Product::className(), ['id_product' => 'id_product'])->viaTable('{price}', ['id_price_category' => 'id_price_category']);
    }
}
