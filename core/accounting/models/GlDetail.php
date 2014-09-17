<?php

namespace core\accounting\models;

use Yii;

/**
 * This is the model class for table "gl_detail".
 *
 * @property integer $id_gl_detail
 * @property integer $id_gl
 * @property integer $id_coa
 * @property double $amount
 *
 * @property GlHeader $glHeader
 * @property Coa $coa
 */
class GlDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gl_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_gl', 'id_coa', 'amount'], 'required'],
            [['id_gl', 'id_coa'], 'integer'],
            [['amount'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_gl_detail' => 'Id Gl Detail',
            'id_gl' => 'Id Gl',
            'id_coa' => 'Id Coa',
            'amount' => 'Amount',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGlHeader()
    {
        return $this->hasOne(GlHeader::className(), ['id_gl' => 'id_gl']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoa()
    {
        return $this->hasOne(Coa::className(), ['id_coa' => 'id_coa']);
    }
}
