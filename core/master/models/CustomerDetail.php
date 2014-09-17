<?php

namespace core\master\models;

use Yii;

/**
 * This is the model class for table "customer_detail".
 *
 * @property integer $id_customer
 * @property integer $id_distric
 * @property string $addr1
 * @property string $addr2
 * @property double $latitude
 * @property double $longtitude
 * @property integer $id_kab
 * @property integer $id_kec
 * @property integer $id_kel
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property Customer $idCustomer
 */
class CustomerDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_customer', 'created_by', 'updated_by'], 'required'],
            [['id_customer', 'id_distric', 'id_kab', 'id_kec', 'id_kel', 'created_by', 'updated_by'], 'integer'],
            [['latitude', 'longtitude'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['addr1', 'addr2'], 'string', 'max' => 128]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_customer' => 'Id Customer',
            'id_distric' => 'Id Distric',
            'addr1' => 'Addr1',
            'addr2' => 'Addr2',
            'latitude' => 'Latitude',
            'longtitude' => 'Longtitude',
            'id_kab' => 'Id Kab',
            'id_kec' => 'Id Kec',
            'id_kel' => 'Id Kel',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdCustomer()
    {
        return $this->hasOne(Customer::className(), ['id_customer' => 'id_customer']);
    }
}
