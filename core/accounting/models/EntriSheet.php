<?php

namespace core\accounting\models;

use Yii;

/**
 * This is the model class for table "entri_sheet".
 *
 * @property string $cd_esheet
 * @property string $nm_esheet
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property EntriSheetDtl[] $entriSheetDtls
 */
class EntriSheet extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'entri_sheet';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cd_esheet', 'nm_esheet', 'created_by', 'updated_by'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['cd_esheet'], 'string', 'max' => 16],
            [['nm_esheet'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cd_esheet' => 'Cd Esheet',
            'nm_esheet' => 'Nm Esheet',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEntriSheetDtls()
    {
        return $this->hasMany(EntriSheetDtl::className(), ['cd_esheet' => 'cd_esheet']);
    }
}
