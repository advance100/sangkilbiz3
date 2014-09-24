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
     *
     * @var string 
     */
    public $modelClass = 'core\purchase\models\Purchase';

    /**
     *
     * @var string 
     */
    public $prefixEventName = 'e_purchase';

    /**
     * Use to create purchase.
     * @param array $data values use to create purchase model. It must contain
     *
     * @param \core\purchase\models\Purchase $model
     *
     * @return \core\purchase\models\Purchase
     * @throws \Exception
     */
    public function create($data, $model = null)
    {
        /* @var $model MPurchase */
        $model = $model ? : $this->createNewModel();
        $success = false;
        $model->scenario = MPurchase::SCENARIO_DEFAULT;
        $model->load($data, '');

        if (!empty($data['details'])) {
            $this->fire('_create', [$model]);
            $success = $model->save();
            $success = $model->saveRelated('purchaseDtls', $data, $success, 'details');
            if ($success) {
                $this->fire('_created', [$model]);
                $transaction->commit();
            } else {
                if ($model->hasRelatedErrors('purchaseDtls')) {
                    $model->addError('details', 'Details validation error');
                }
            }
        } else {
            $model->validate();
            $model->addError('details', 'Details cannot be blank');
        }

        return $this->processOutput($success, $model);
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
    public function update($id, $data, $model = null)
    {
        $model = $model ? : $this->findModel($id);

        $success = false;
        $model->scenario = MPurchase::SCENARIO_DEFAULT;
        $model->load($data, '');

        if (!isset($data['details']) || $data['details'] !== []) {
            $this->fire('_update', [$model]);
            $success = $model->save();
            if (!empty($data['details'])) {
                $success = $model->saveRelated('purchaseDtls', $data, $success, 'details');
            }
            if ($success) {
                $this->fire('_updated', [$model]);
            } else {
                if ($model->hasRelatedErrors('purchaseDtls')) {
                    $model->addError('details', 'Details validation error');
                }
            }
        } else {
            $model->validate();
            $model->addError('details', 'Details cannot be blank');
        }

        return $this->processOutput($success, $model);
    }

    /**
     *
     * @param  string                         $id
     * @param  array                          $data
     * @param  \core\purchase\models\Purchase $model
     * @return \core\purchase\models\Purchase
     * @throws \Exception
     */
    public function receive($id, $data = [], $model = null)
    {
        $model = $model ? : $this->findModel($id);

        $success = true;
        $model->scenario = MPurchase::SCENARIO_DEFAULT;
        $model->load($data, '');
        $model->status = MPurchase::STATUS_RECEIVE;
        $this->fire('_receive', [$model]);
        $purchaseDtls = ArrayHelper::index($model->purchaseDtls, 'id_product');
        if (!empty($data['details'])) {
            $this->fire('_receive_head', [$model]);
            foreach ($data['details'] as $dataDetail) {
                $index = $dataDetail['id_product'];
                $detail = $purchaseDtls[$index];
                $detail->scenario = MPurchase::SCENARIO_RECEIVE;
                $detail->load($dataDetail, '');
                $success = $success && $detail->save();
                $this->fire('_receive_body', [$model, $detail]);
                $purchaseDtls[$index] = $detail;
            }
            $model->populateRelation('purchaseDtls', array_values($purchaseDtls));
            $this->fire('_receive_end', [$model]);
        }
        $allReceived = true;
        foreach ($purchaseDtls as $detail) {
            $allReceived = $allReceived && $detail->purch_qty == $detail->purch_qty_receive;
        }
        if ($allReceived) {
            $model->status = MPurchase::STATUS_RECEIVED;
        }
        if ($success && $model->save()) {
            $this->fire('_received', [$model]);
        } else {
            $success = false;
        }

        return $this->processOutput($success, $model);
    }
}