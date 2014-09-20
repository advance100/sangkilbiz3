<?php

namespace core\accounting\models;

use Yii;

/**
 * This is the model class for table "gl_header".
 *
 * @property integer $id_gl
 * @property string $gl_num
 * @property string $gl_date
 * @property integer $id_periode
 * @property integer $id_branch
 * @property integer $type_reff
 * @property integer $id_reff
 * @property string $description
 * @property integer $status
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 *
 * @property GlDetail[] $glDetails
 * @property AccPeriode $periode
 */
class GlHeader extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%gl_header}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gl_num', 'gl_date', 'id_periode', 'id_branch', 'type_reff', 'description', 'status'], 'required'],
            [['gl_date', 'created_at', 'updated_at'], 'safe'],
            [['id_periode', 'id_branch', 'type_reff', 'id_reff', 'status', 'created_by', 'updated_by'], 'integer'],
            [['gl_num'], 'string', 'max' => 16],
            [['description'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_gl' => 'Id Gl',
            'gl_num' => 'Gl Num',
            'gl_date' => 'Gl Date',
            'id_periode' => 'Id Periode',
            'id_branch' => 'Id Branch',
            'type_reff' => 'Type Reff',
            'id_reff' => 'Id Reff',
            'description' => 'Description',
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
    public function getGlDetails()
    {
        return $this->hasMany(GlDetail::className(), ['id_gl' => 'id_gl']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeriode()
    {
        return $this->hasOne(AccPeriode::className(), ['id_periode' => 'id_periode']);
    }
}
