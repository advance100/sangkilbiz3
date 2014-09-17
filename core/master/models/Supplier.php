<?php

namespace core\master\models;

use Yii;

/**
 * This is the model class for table "supplier".
 *
 * @property integer $id_supplier
 * @property string $cd_supplier
 * @property string $nm_supplier
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property ProductSupplier[] $productSuppliers
 * @property Product[] $idProducts
 */
class Supplier extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'supplier';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cd_supplier', 'nm_supplier', 'created_by', 'updated_by'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['cd_supplier'], 'string', 'max' => 4],
            [['nm_supplier'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_supplier' => 'Id Supplier',
            'cd_supplier' => 'Cd Supplier',
            'nm_supplier' => 'Nm Supplier',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductSuppliers()
    {
        return $this->hasMany(ProductSupplier::className(), ['id_supplier' => 'id_supplier']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdProducts()
    {
        return $this->hasMany(Product::className(), ['id_product' => 'id_product'])->viaTable('{product_supplier}', ['id_supplier' => 'id_supplier']);
    }
}
