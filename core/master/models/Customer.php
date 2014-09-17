<?php

namespace core\master\models;

use Yii;

/**
 * This is the model class for table "customer".
 *
 * @property integer $id_customer
 * @property string $cd_customer
 * @property string $nm_customer
 * @property string $contact_name
 * @property string $contact_number
 * @property integer $status
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property CustomerDetail $customerDetail
 */
class Customer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cd_customer', 'nm_customer', 'status', 'created_by', 'updated_by'], 'required'],
            [['status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['cd_customer'], 'string', 'max' => 4],
            [['nm_customer', 'contact_name', 'contact_number'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_customer' => 'Id Customer',
            'cd_customer' => 'Cd Customer',
            'nm_customer' => 'Nm Customer',
            'contact_name' => 'Contact Name',
            'contact_number' => 'Contact Number',
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
    public function getCustomerDetail()
    {
        return $this->hasOne(CustomerDetail::className(), ['id_customer' => 'id_customer']);
    }
}
