<?php

namespace core\accounting\models;

use Yii;

/**
 * This is the model class for table "invoice_dtl".
 *
 * @property integer $id_invoice
 * @property integer $type_reff
 * @property integer $id_reff
 * @property string $description
 * @property double $trans_value
 *
 * @property Invoice $invoice
 */
class InvoiceDtl extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice_dtl';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_invoice', 'type_reff', 'id_reff', 'trans_value'], 'required'],
            [['id_invoice', 'type_reff', 'id_reff'], 'integer'],
            [['trans_value'], 'number'],
            [['description'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_invoice' => 'Id Invoice',
            'type_reff' => 'Type Reff',
            'id_reff' => 'Id Reff',
            'description' => 'Description',
            'trans_value' => 'Trans Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id_invoice' => 'id_invoice']);
    }
}
