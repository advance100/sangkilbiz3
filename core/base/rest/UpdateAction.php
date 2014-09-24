<?php

namespace core\base\rest;

use Yii;

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
        try {
            $transaction = Yii::$app->db->beginTransaction();
            $model = $this->api->update($id, Yii::$app->getRequest()->getBodyParams());
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