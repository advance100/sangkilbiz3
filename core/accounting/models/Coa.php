<?php

namespace core\accounting\models;

use Yii;

/**
 * This is the model class for table "coa".
 *
 * @property integer $id_coa
 * @property integer $id_parent
 * @property string $cd_account
 * @property string $nm_account
 * @property integer $coa_type
 * @property string $normal_balance
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property Coa $idParent
 * @property Coa[] $coas
 * @property EntriSheetDtl[] $entriSheetDtls
 * @property GlDetail[] $glDetails
 */
class Coa extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'coa';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_parent', 'coa_type', 'created_by', 'updated_by'], 'integer'],
            [['cd_account', 'nm_account', 'coa_type', 'normal_balance', 'created_by', 'updated_by'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['cd_account'], 'string', 'max' => 16],
            [['nm_account'], 'string', 'max' => 64],
            [['normal_balance'], 'string', 'max' => 1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_coa' => 'Id Coa',
            'id_parent' => 'Id Parent',
            'cd_account' => 'Cd Account',
            'nm_account' => 'Nm Account',
            'coa_type' => 'Coa Type',
            'normal_balance' => 'Normal Balance',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdParent()
    {
        return $this->hasOne(Coa::className(), ['id_coa' => 'id_parent']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoas()
    {
        return $this->hasMany(Coa::className(), ['id_parent' => 'id_coa']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntriSheetDtls()
    {
        return $this->hasMany(EntriSheetDtl::className(), ['id_coa' => 'id_coa']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGlDetails()
    {
        return $this->hasMany(GlDetail::className(), ['id_coa' => 'id_coa']);
    }
}
