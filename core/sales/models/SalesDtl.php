<?php

namespace core\sales\models;

use Yii;
use core\master\models\ProductUom;

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
 * @property double $sales_qty_release
 *
 * @property Sales $sales
 */
class SalesDtl extends \yii\db\ActiveRecord
{
    /**
     * @var integer Warehouse for release.
     */
    public $id_warehouse;

    /**
     * @var double Quantity for release.
     */
    public $qty_release;

    /**
     * @var integer Uom for receive.
     */
    public $id_uom_release;

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
            [['sales_qty', 'sales_price', 'cogs', 'discount', 'tax'], 'number'],
            [['id_warehouse', 'qty_release', 'id_uom_release'], 'safe', 'on' => Sales::SCENARIO_RELEASE],
            [['id_warehouse'], 'required', 'on' => Sales::SCENARIO_RELEASE, 'when' => [$this, 'whenReleased']],
            [['qty_receive'], 'double', 'on' => Sales::SCENARIO_RELEASE],
            [['qty_receive'], 'convertRelease', 'on' => Sales::SCENARIO_RELEASE]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        foreach ($scenarios[Sales::SCENARIO_RELEASE] as $i => $attr) {
            if (!in_array($attr, ['id_warehouse', 'qty_release', 'id_uom_release']) && $attr[0] != '!') {
                $scenarios[Sales::SCENARIO_RELEASE][$i] = '!' . $attr;
            }
        }

        return $scenarios;
    }

    public function convertRelease($attribute)
    {
        if ($this->id_uom_release === null || $this->id_uom == $this->id_uom_release) {
            $this->sales_qty_release += $this->qty_release;
        } else {
            $uoms = ProductUom::find()->where(['id_product' => $this->id_product])->indexBy('id_uom')->all();
            $this->sales_qty_release += $this->qty_release * $uoms[$this->id_uom_release]->isi / $uoms[$this->id_uom]->isi;
        }
        if ($this->sales_qty_release > $this->sales_qty) {
            $this->addError($attribute, 'Total qty release large than sales qty');
        }
    }

    /**
     * Check when purchase is received.
     * Indicated with `qty_receive` is setted.
     *
     * @return boolean
     */
    public function whenReleased()
    {
        return $this->qty_release !== null && $this->qty_release !== '';
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
    public function getSales()
    {
        return $this->hasOne(Sales::className(), ['id_sales' => 'id_sales']);
    }
}
