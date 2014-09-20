<?php

namespace core\purchase\models;

use Yii;

/**
 * This is the model class for table "purchase".
 *
 * @property integer $id_purchase
 * @property string $purchase_num
 * @property integer $id_supplier
 * @property integer $id_branch
 * @property string $purchase_date
 * @property double $purchase_value
 * @property double $discount
 * @property integer $status
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 * @property string $purchaseDate
 *
 * @property PurchaseDtl[] $purchaseDtls
 */
class Purchase extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 1;
    const STATUS_RECEIVE = 2;
    const STATUS_RECEIVED = 3;

    /**
     * Scenario when purchase received.
     */
    const SCENARIO_RECEIVE = 'receive';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%purchase}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => self::STATUS_DRAFT],
            [['id_supplier', 'id_branch', 'purchase_date', 'purchase_value'], 'required'],
            [['id_supplier', 'id_branch', 'status', 'created_by', 'updated_by'], 'integer'],
            [['purchase_date', 'created_at', 'updated_at'], 'safe'],
            [['purchase_value', 'item_discount'], 'number'],
            [['purchase_num'], 'string', 'max' => 16]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_purchase' => 'Id Purchase',
            'purchase_num' => 'Purchase Num',
            'id_supplier' => 'Id Supplier',
            'id_branch' => 'Id Branch',
            'purchase_date' => 'Purchase Date',
            'purchase_value' => 'Purchase Value',
            'item_discount' => 'Item Discount',
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
    public function getPurchaseDtls()
    {
        return $this->hasMany(PurchaseDtl::className(), ['id_purchase' => 'id_purchase']);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return[
            'BizTimestampBehavior',
            'BizBlameableBehavior',
            [
                'class' => 'mdm\autonumber\Behavior',
                'digit' => 6,
                'attribute' => 'purchase_num',
                'value' => 'PU' . date('y.?')
            ],
            [
                'class' => 'mdm\converter\DateConverter',
                'attributes' => [
                    'purchaseDate' => 'purchase_date',
                ]
            ],
            'BizStatusConverter',
            'mdm\behaviors\ar\RelatedBehavior',
        ];
    }
}
