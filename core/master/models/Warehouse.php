<?php

namespace core\master\models;

use Yii;

/**
 * This is the model class for table "warehouse".
 *
 * @property integer $id_warehouse
 * @property integer $id_branch
 * @property string $cd_whse
 * @property string $nm_whse
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property ProductStock[] $productStocks
 * @property Product[] $idProducts
 * @property Branch $idBranch
 */
class Warehouse extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'warehouse';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_branch', 'cd_whse', 'nm_whse', 'created_by', 'updated_by'], 'required'],
            [['id_branch', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['cd_whse'], 'string', 'max' => 4],
            [['nm_whse'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_warehouse' => 'Id Warehouse',
            'id_branch' => 'Id Branch',
            'cd_whse' => 'Cd Whse',
            'nm_whse' => 'Nm Whse',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductStocks()
    {
        return $this->hasMany(ProductStock::className(), ['id_warehouse' => 'id_warehouse']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdProducts()
    {
        return $this->hasMany(Product::className(), ['id_product' => 'id_product'])->viaTable('{product_stock}', ['id_warehouse' => 'id_warehouse']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdBranch()
    {
        return $this->hasOne(Branch::className(), ['id_branch' => 'id_branch']);
    }
}
