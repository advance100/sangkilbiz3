<?php

namespace core\accounting\models;

use Yii;

/**
 * This is the model class for table "payment".
 *
 * @property integer $id_payment
 * @property string $payment_num
 * @property string $payment_date
 * @property integer $payment_type
 * @property integer $status
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property PaymentDtl[] $paymentDtls
 * @property Invoice[] $invoices
 */
class Payment extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;
    const STATUS_POSTED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%payment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payment_num', 'payment_date', 'payment_type', 'created_by', 'updated_by'], 'required'],
            [['payment_date', 'created_at', 'updated_at'], 'safe'],
            [['payment_type', 'created_by', 'updated_by'], 'integer'],
            [['payment_num'], 'string', 'max' => 16]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_payment' => 'Id Payment',
            'payment_num' => 'Payment Num',
            'payment_date' => 'Payment Date',
            'payment_type' => 'Payment Type',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentDtls()
    {
        return $this->hasMany(PaymentDtl::className(), ['id_payment' => 'id_payment']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoice::className(), ['id_invoice' => 'id_invoice'])->viaTable('{payment_dtl}', ['id_payment' => 'id_payment']);
    }
}
