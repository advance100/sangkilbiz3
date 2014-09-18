<?php

namespace core\accounting\models;

use Yii;

/**
 * This is the model class for table "invoice".
 *
 * @property integer $id_invoice
 * @property string $invoice_num
 * @property string $invoice_date
 * @property string $due_date
 * @property integer $invoice_type
 * @property integer $id_vendor
 * @property double $invoice_value
 * @property integer $status
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property InvoiceDtl[] $invoiceDtls
 * @property PaymentDtl[] $paymentDtls
 * @property Payment[] $payments
 */
class Invoice extends \yii\db\ActiveRecord
{
    const TYPE_IN = 1;
    const TYPE_OUT = 2;
    
    const STATUS_DRAFT = 1;
    const STATUS_POSTED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoice_num', 'invoice_date', 'due_date', 'invoice_type', 'id_vendor', 'invoice_value', 'status'], 'required'],
            [['invoice_date', 'due_date', 'created_at', 'updated_at'], 'safe'],
            [['invoice_type', 'id_vendor', 'status', 'created_by', 'updated_by'], 'integer'],
            [['invoice_value'], 'number'],
            [['invoice_num'], 'string', 'max' => 16]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_invoice' => 'Id Invoice',
            'invoice_num' => 'Invoice Num',
            'invoice_date' => 'Invoice Date',
            'due_date' => 'Due Date',
            'invoice_type' => 'Invoice Type',
            'id_vendor' => 'Id Vendor',
            'invoice_value' => 'Invoice Value',
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
    public function getInvoiceDtls()
    {
        return $this->hasMany(InvoiceDtl::className(), ['id_invoice' => 'id_invoice']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentDtls()
    {
        return $this->hasMany(PaymentDtl::className(), ['id_invoice' => 'id_invoice']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Payment::className(), ['id_payment' => 'id_payment'])->via('paymentDtls');
    }
}
