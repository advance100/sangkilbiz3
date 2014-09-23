<?php

namespace rest\base;

use Yii;
use yii\web\ServerErrorHttpException;

/**
 * Description of UpdateAction
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class UpdateAction extends Action
{

    public function run($id)
    {
        /* @var $model \yii\db\ActiveRecord */
        $helperClass = $this->helperClass;
        try {
            $transaction = Yii::$app->db->beginTransaction();
            $model = $helperClass::update($id, Yii::$app->getRequest()->getBodyParams());
            if (!$model->hasErrors()) {
                $transaction->commit();
            } else {
                $transaction->rollBack();
            }
        } catch (\Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        }
        return $model;
    }
}