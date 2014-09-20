<?php

namespace core\inventory\models;

use Yii;

/**
 * This is the model class for table "transfer".
 *
 * @property integer $id_transfer
 * @property string $transfer_num
 * @property integer $id_branch
 * @property integer $id_branch_dest
 * @property string $transfer_date
 * @property integer $status
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property TransferDtl[] $transferDtls
 */
class Transfer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%transfer}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['transfer_num', 'id_branch', 'id_branch_dest', 'transfer_date', 'status', 'created_by', 'updated_by'], 'required'],
            [['id_branch', 'id_branch_dest', 'status', 'created_by', 'updated_by'], 'integer'],
            [['transfer_date', 'created_at', 'updated_at'], 'safe'],
            [['transfer_num'], 'string', 'max' => 16]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_transfer' => 'Id Transfer',
            'transfer_num' => 'Transfer Num',
            'id_branch' => 'Id Branch',
            'id_branch_dest' => 'Id Branch Dest',
            'transfer_date' => 'Transfer Date',
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
    public function getTransferDtls()
    {
        return $this->hasMany(TransferDtl::className(), ['id_transfer' => 'id_transfer']);
    }
}
