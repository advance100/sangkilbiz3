<?php

namespace core\purchase\models;

use Yii;
use core\master\models\ProductUom;

/**
 * This is the model class for table "purchase_dtl".
 *
 * @property integer $id_purchase
 * @property integer $id_product
 * @property integer $id_uom
 * @property double $purch_qty
 * @property double $purch_price
 * @property double $purch_qty_receive
 *
 * @property Purchase $purchase
 */
class PurchaseDtl extends \yii\db\ActiveRecord
{
    /**
     * @var integer Warehouse for receive. 
     */
    public $id_warehouse;

    /**
     * @var double Quantity for receive.
     */
    public $qty_receive;

    /**
     * @var integer Uom for receive. 
     */
    public $id_uom_receive;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'purchase_dtl';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_purchase', 'id_product', 'id_uom', 'purch_qty', 'purch_price'], 'required'],
            [['id_purchase', 'id_product', 'id_uom'], 'integer'],
            [['purch_qty', 'purch_price'], 'number'],
            [['id_warehouse', 'qty_receive', 'id_uom_receive'], 'safe', 'on' => Purchase::SCENARIO_RECEIVE],
            [['id_warehouse'], 'required', 'on' => Purchase::SCENARIO_RECEIVE, 'when' => [$this, 'whenReceived']],
            [['qty_receive'], 'double', 'on' => Purchase::SCENARIO_RECEIVE],
            [['qty_receive'], 'convertReceive', 'on' => Purchase::SCENARIO_RECEIVE]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        foreach ($scenarios[Purchase::SCENARIO_RECEIVE] as $i => $attr) {
            if (!in_array($attr, ['id_warehouse', 'qty_receive', 'id_uom_receive']) && $attr[0] != '!') {
                $scenarios[Purchase::SCENARIO_RECEIVE][$i] = '!' . $attr;
            }
        }
        return $scenarios;
    }

    public function convertReceive($attribute)
    {
        if ($this->id_uom_receive === null || $this->id_uom == $this->id_uom_receive) {
            $this->purch_qty_receive += $this->qty_receive;
        } else {
            $uoms = ProductUom::find()->where(['id_product' => $this->id_product])->indexBy('id_uom')->all();
            $this->purch_qty_receive += $this->qty_receive * $uoms[$this->id_uom_receive]->isi / $uoms[$this->id_uom]->isi;
        }
        if ($this->purch_qty_receive > $this->purch_qty) {
            $this->addError($attribute, 'Total qty receive large than purch qty');
        }
    }

    /**
     * Check when purchase is received.
     * Indicated with `qty_receive` is setted.
     * 
     * @return boolean
     */
    public function whenReceived()
    {
        return $this->qty_receive !== null && $this->qty_receive !== '';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_purchase' => 'Id Purchase',
            'id_product' => 'Id Product',
            'id_uom' => 'Id Uom',
            'purch_qty' => 'Purch Qty',
            'purch_price' => 'Purch Price',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPurchase()
    {
        return $this->hasOne(Purchase::className(), ['id_purchase' => 'id_purchase']);
    }
}