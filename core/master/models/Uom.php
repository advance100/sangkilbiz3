<?php

namespace core\master\models;

use Yii;

/**
 * This is the model class for table "uom".
 *
 * @property integer $id_uom
 * @property string $cd_uom
 * @property string $nm_uom
 * @property integer $isi
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property ProductUom[] $productUoms
 * @property Product[] $idProducts
 */
class Uom extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'uom';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cd_uom', 'nm_uom', 'isi', 'created_by', 'updated_by'], 'required'],
            [['isi', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['cd_uom'], 'string', 'max' => 4],
            [['nm_uom'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_uom' => 'Id Uom',
            'cd_uom' => 'Cd Uom',
            'nm_uom' => 'Nm Uom',
            'isi' => 'Isi',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductUoms()
    {
        return $this->hasMany(ProductUom::className(), ['id_uom' => 'id_uom']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdProducts()
    {
        return $this->hasMany(Product::className(), ['id_product' => 'id_product'])->viaTable('{product_uom}', ['id_uom' => 'id_uom']);
    }
}
