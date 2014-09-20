<?php

namespace core\accounting\models;

use Yii;

/**
 * This is the model class for table "acc_periode".
 *
 * @property integer $id_periode
 * @property string $nm_periode
 * @property string $date_from
 * @property string $date_to
 * @property integer $status
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property GlHeader[] $glHeaders
 */
class AccPeriode extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%acc_periode}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nm_periode', 'date_from', 'date_to', 'status', 'created_by', 'updated_by'], 'required'],
            [['date_from', 'date_to', 'created_at', 'updated_at'], 'safe'],
            [['status', 'created_by', 'updated_by'], 'integer'],
            [['nm_periode'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_periode' => 'Id Periode',
            'nm_periode' => 'Nm Periode',
            'date_from' => 'Date From',
            'date_to' => 'Date To',
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
    public function getGlHeaders()
    {
        return $this->hasMany(GlHeader::className(), ['id_periode' => 'id_periode']);
    }
}
