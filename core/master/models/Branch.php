<?php

namespace core\master\models;

use Yii;

/**
 * This is the model class for table "branch".
 *
 * @property integer $id_branch
 * @property integer $id_orgn
 * @property string $cd_branch
 * @property string $nm_branch
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property Orgn $idOrgn
 * @property UserToBranch[] $userToBranches
 * @property Warehouse[] $warehouses
 */
class Branch extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'branch';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_orgn', 'cd_branch', 'nm_branch', 'created_by', 'updated_by'], 'required'],
            [['id_orgn', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['cd_branch'], 'string', 'max' => 4],
            [['nm_branch'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_branch' => 'Id Branch',
            'id_orgn' => 'Id Orgn',
            'cd_branch' => 'Cd Branch',
            'nm_branch' => 'Nm Branch',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdOrgn()
    {
        return $this->hasOne(Orgn::className(), ['id_orgn' => 'id_orgn']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserToBranches()
    {
        return $this->hasMany(UserToBranch::className(), ['id_branch' => 'id_branch']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouses()
    {
        return $this->hasMany(Warehouse::className(), ['id_branch' => 'id_branch']);
    }
}
