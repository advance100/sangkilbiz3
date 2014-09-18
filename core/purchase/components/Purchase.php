<?php

namespace core\purchase\components;

use Yii;
use core\purchase\models\Purchase as MPurchase;
use yii\helpers\ArrayHelper;

/**
 * Description of Purchase
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class Purchase extends \core\base\Api
{

    /**
     * @inheritdoc
     */
    public static function modelClass()
    {
        return MPurchase::className();
    }

    /**
     * @inheritdoc
     */
    public static function prefixEventName()
    {
        return 'e_purchase';
    }

    /**
     * Use to create purchase. 
     * @param array $data values use to create purchase model. It must contain
     * 
     * @param \core\purchase\models\Purchase $model
     * 
     * @return \core\purchase\models\Purchase
     * @throws \Exception
     */
    public static function create($data, $model = null)
    {
        $model = $model ? : new MPurchase();
        $success = false;
        $model->scenario = MPurchase::SCENARIO_DEFAULT;
        $model->load($data, '');

        if (!empty($data['details'])) {
            try {
                $transaction = Yii::$app->db->beginTransaction();
                static::trigger('_create', [$model]);
                $success = $model->save();
                $success = $model->saveRelated('purchaseDtls', $data, $success, 'details');
                if ($success) {
                    static::trigger('_created', [$model]);
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                    if ($model->hasRelatedErrors('purchaseDtls')) {
                        $model->addError('details', 'Details validation error');
                    }
                }
            } catch (\Exception $exc) {
                $transaction->rollBack();
                throw $exc;
            }
        } else {
            $model->validate();
            $model->addError('details', 'Details cannot be blank');
        }
        return static::processOutput($success, $model);
    }

    /**
     * Use to update existing purchase. 
     * @param array $data values use to create purchase model. It must contain
     * 
     * @param \core\purchase\models\Purchase $model
     * 
     * @return \core\purchase\models\Purchase
     * @throws \Exception
     */
    public static function update($id, $data, $model = null)
    {
        $model = $model ? : static::findModel($id);

        $success = false;
        $model->scenario = MPurchase::SCENARIO_DEFAULT;
        $model->load($data, '');

        if (!isset($data['details']) || $data['details'] !== []) {
            try {
                $transaction = Yii::$app->db->beginTransaction();
                static::trigger('_update', [$model]);
                $success = $model->save();
                if (!empty($data['details'])) {
                    $success = $model->saveRelated('purchaseDtls', $data, $success, 'details');
                }
                if ($success) {
                    static::trigger('_updated', [$model]);
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                    if ($model->hasRelatedErrors('purchaseDtls')) {
                        $model->addError('details', 'Details validation error');
                    }
                }
            } catch (\Exception $exc) {
                $transaction->rollBack();
                throw $exc;
            }
        } else {
            $model->validate();
            $model->addError('details', 'Details cannot be blank');
        }
        return static::processOutput($success, $model);
    }

    /**
     * 
     * @param string $id
     * @param array $data
     * @param \core\purchase\models\Purchase $model
     * @return \core\purchase\models\Purchase
     * @throws \Exception
     */
    public static function receive($id, $data = [], $model = null)
    {
        $model = $model ? : static::findModel($id);

        $success = true;
        $model->scenario = MPurchase::SCENARIO_DEFAULT;
        $model->load($data, '');
        $model->status = MPurchase::STATUS_RECEIVE;
        try {
            $transaction = Yii::$app->db->beginTransaction();
            static::trigger('_receive', [$model]);
            $purchaseDtls = ArrayHelper::index($model->purchaseDtls,'id_product');
            if (!empty($data['details'])) {
                static::trigger('_receive_head', [$model]);
                foreach ($data['details'] as $dataDetail) {
                    $index = $dataDetail['id_product'];
                    $detail = $purchaseDtls[$index];
                    $detail->scenario = MPurchase::SCENARIO_RECEIVE;
                    $detail->load($dataDetail, '');
                    $success = $success && $detail->save();
                    static::trigger('_receive_body', [$model,$detail]);
                    $purchaseDtls[$index] = $detail;
                }
                $model->populateRelation('purchaseDtls', array_values($purchaseDtls));
                static::trigger('_receive_end', [$model]);
            }
            $allReceived = true;
            foreach ($purchaseDtls as $detail) {
                $allReceived = $allReceived && $detail->purch_qty == $detail->purch_qty_receive;
            }
            if ($allReceived) {
                $model->status = MPurchase::STATUS_RECEIVED;
            }
            if ($success && $model->save()) {
                static::trigger('_received', [$model]);
                $transaction->commit();
            } else {
                $transaction->rollBack();
                $success = false;
            }
        } catch (\Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        } 
        return static::processOutput($success, $model);
    }
}