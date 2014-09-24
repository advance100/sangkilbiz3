<?php

namespace core\sales\controllers;

use Yii;
use core\sales\components\Sales as ApiSales;

/**
 * Description of PurchaseController
 *
 * @property ApiSales $api
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class SalesController extends \core\base\rest\Controller
{
    /**
     * @inheritdoc
     */
    public $api = 'core\sales\components\Sales';

    public function release($id)
    {
        try {
            $transaction = Yii::$app->db->beginTransaction();
            $model = $this->api->release($id, Yii::$app->getRequest()->getBodyParams());
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