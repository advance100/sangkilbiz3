<?php

namespace core\master\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property integer $id_product
 * @property integer $id_group
 * @property integer $id_category
 * @property string $cd_product
 * @property string $nm_product
 * @property integer $status
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property Cogs $cogs
 * @property Price[] $prices
 * @property PriceCategory[] $idPriceCategories
 * @property ProductGroup $idGroup
 * @property Category $idCategory
 * @property ProductChild[] $productChildren
 * @property ProductStock[] $productStocks
 * @property Warehouse[] $idWarehouses
 * @property ProductSupplier[] $productSuppliers
 * @property Supplier[] $idSuppliers
 * @property ProductUom[] $productUoms
 * @property Uom[] $idUoms
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_group', 'id_category', 'cd_product', 'nm_product', 'status', 'created_by', 'updated_by'], 'required'],
            [['id_group', 'id_category', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['cd_product'], 'string', 'max' => 13],
            [['nm_product'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_product' => 'Id Product',
            'id_group' => 'Id Group',
            'id_category' => 'Id Category',
            'cd_product' => 'Cd Product',
            'nm_product' => 'Nm Product',
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
    public function getCogs()
    {
        return $this->hasOne(Cogs::className(), ['id_product' => 'id_product']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrices()
    {
        return $this->hasMany(Price::className(), ['id_product' => 'id_product']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdPriceCategories()
    {
        return $this->hasMany(PriceCategory::className(), ['id_price_category' => 'id_price_category'])->viaTable('{price}', ['id_product' => 'id_product']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdGroup()
    {
        return $this->hasOne(ProductGroup::className(), ['id_group' => 'id_group']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdCategory()
    {
        return $this->hasOne(Category::className(), ['id_category' => 'id_category']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductChildren()
    {
        return $this->hasMany(ProductChild::className(), ['id_product' => 'id_product']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductStocks()
    {
        return $this->hasMany(ProductStock::className(), ['id_product' => 'id_product']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdWarehouses()
    {
        return $this->hasMany(Warehouse::className(), ['id_warehouse' => 'id_warehouse'])->viaTable('{product_stock}', ['id_product' => 'id_product']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductSuppliers()
    {
        return $this->hasMany(ProductSupplier::className(), ['id_product' => 'id_product']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdSuppliers()
    {
        return $this->hasMany(Supplier::className(), ['id_supplier' => 'id_supplier'])->viaTable('{product_supplier}', ['id_product' => 'id_product']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductUoms()
    {
        return $this->hasMany(ProductUom::className(), ['id_product' => 'id_product']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdUoms()
    {
        return $this->hasMany(Uom::className(), ['id_uom' => 'id_uom'])->viaTable('{product_uom}', ['id_product' => 'id_product']);
    }
}
