<?php

namespace core\inventory\models;

use Yii;

/**
 * This is the model class for table "transfer_dtl".
 *
 * @property integer $id_transfer
 * @property integer $id_product
 * @property integer $id_uom
 * @property double $transfer_qty
 * @property double $transfer_qty_send
 * @property double $transfer_qty_receive
 *
 * @property Transfer $transfer
 */
class TransferDtl extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%transfer_dtl}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_transfer', 'id_product', 'id_uom'], 'required'],
            [['id_transfer', 'id_product', 'id_uom'], 'integer'],
            [['transfer_qty', 'transfer_qty_send', 'transfer_qty_receive'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_transfer' => 'Id Transfer',
            'id_product' => 'Id Product',
            'id_uom' => 'Id Uom',
            'transfer_qty' => 'Transfer Qty',
            'transfer_qty_send' => 'Transfer Qty Send',
            'transfer_qty_receive' => 'Transfer Qty Receive',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransfer()
    {
        return $this->hasOne(Transfer::className(), ['id_transfer' => 'id_transfer']);
    }
}
