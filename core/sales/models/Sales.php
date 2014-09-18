<?php

namespace core\sales\models;

use Yii;

/**
 * This is the model class for table "sales".
 *
 * @property integer $id_sales
 * @property string $sales_num
 * @property integer $id_branch
 * @property integer $id_customer
 * @property string $sales_date
 * @property double $sales_value
 * @property double $discount
 * @property integer $status
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property SalesDtl[] $salesDtls
 */
class Sales extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;
    const STATUS_RELEASE = 2;
    const STATUS_RELEASED = 3;
    
    const SCENARIO_RELEASE = 'release';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sales';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sales_num', 'id_branch', 'sales_date', 'sales_value', 'status', 'created_by', 'updated_by'], 'required'],
            [['id_branch', 'id_customer', 'status', 'created_by', 'updated_by'], 'integer'],
            [['sales_date', 'created_at', 'updated_at'], 'safe'],
            [['sales_value', 'discount'], 'number'],
            [['sales_num'], 'string', 'max' => 16]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_sales' => 'Id Sales',
            'sales_num' => 'Sales Num',
            'id_branch' => 'Id Branch',
            'id_customer' => 'Id Customer',
            'sales_date' => 'Sales Date',
            'sales_value' => 'Sales Value',
            'discount' => 'Discount',
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
    public function getSalesDtls()
    {
        return $this->hasMany(SalesDtl::className(), ['id_sales' => 'id_sales']);
    }
}
