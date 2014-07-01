<?php

namespace biz\master\models;

use Yii;

/**
 * This is the model class for table "product_uom".
 *
 * @property integer $id_puom
 * @property integer $id_product
 * @property integer $id_uom
 * @property integer $isi
 * @property string $create_at
 * @property integer $create_by
 * @property string $update_at
 * @property integer $update_by
 *
 * @property Uom $idUom
 * @property Product $idProduct
 */
class ProductUom extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product_uom}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_product', 'id_uom', 'isi'], 'required'],
            [['id_product', 'id_uom', 'isi'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_puom' => 'Id Puom',
            'id_product' => 'Id Product',
            'id_uom' => 'Id Uom',
            'isi' => 'Isi',
            'create_at' => 'Create At',
            'create_by' => 'Create By',
            'update_at' => 'Update At',
            'update_by' => 'Update By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdUom()
    {
        return $this->hasOne(Uom::className(), ['id_uom' => 'id_uom']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdProduct()
    {
        return $this->hasOne(Product::className(), ['id_product' => 'id_product']);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'BizTimestampBehavior',
            'BizBlameableBehavior',
        ];
    }
}