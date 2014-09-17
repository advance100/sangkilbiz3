<?php

namespace core\master\models;

use Yii;

/**
 * This is the model class for table "orgn".
 *
 * @property integer $id_orgn
 * @property string $cd_orgn
 * @property string $nm_orgn
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property Branch[] $branches
 */
class Orgn extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orgn';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cd_orgn', 'nm_orgn', 'created_by', 'updated_by'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['cd_orgn'], 'string', 'max' => 4],
            [['nm_orgn'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_orgn' => 'Id Orgn',
            'cd_orgn' => 'Cd Orgn',
            'nm_orgn' => 'Nm Orgn',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBranches()
    {
        return $this->hasMany(Branch::className(), ['id_orgn' => 'id_orgn']);
    }
}
