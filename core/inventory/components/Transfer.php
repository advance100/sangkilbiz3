<?php

namespace core\inventory\components;

use Yii;
use core\inventory\models\Transfer as MTransfer;
use core\inventory\models\TransferDtl;
use yii\helpers\ArrayHelper;

/**
 * Description of InventoryTransfer
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class Transfer extends \core\base\Api
{

    public static function modelClass()
    {
        return MTransfer::className();
    }

    public static function prefixEventName()
    {
        return 'e_transfer';
    }

    /**
     * 
     * @param array $data
     * @param type $model
     * @return type
     * @throws \Exception
     */
    public static function create($data, $model = null)
    {
        $model = $model ? : new MTransfer();
        $success = false;
        $model->scenario = MTransfer::SCENARIO_DEFAULT;
        $model->load($data, '');

        if (!empty($data['details'])) {
            try {
                $transaction = Yii::$app->db->beginTransaction();
                static::trigger('_create', [$model]);
                $success = $model->save();
                $success = $model->saveRelated('transferDtls', $data, $success, 'details');
                if ($success) {
                    static::trigger('_created', [$model]);
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                    if ($model->hasRelatedErrors('transferDtls')) {
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

    public static function update($id, $data, $model = null)
    {
        $model = $model ? : static::findModel($id);

        $success = false;
        $model->scenario = MTransfer::SCENARIO_DEFAULT;
        $model->load($data, '');

        if (!isset($data['details']) || $data['details'] !== []) {
            try {
                $transaction = Yii::$app->db->beginTransaction();
                static::trigger('_update', [$model]);
                $success = $model->save();
                if (!empty($data['details'])) {
                    $success = $model->saveRelated('transferDtls', $data, $success, 'details');
                }
                if ($success) {
                    static::trigger('_updated', [$model]);
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                    if ($model->hasRelatedErrors('transferDtls')) {
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
     * @param MTransfer $model
     * @return mixed
     * @throws \Exception
     */
    public static function release($id, $data = [], $model = null)
    {
        $model = $model ? : static::findModel($id);

        $success = true;
        $model->scenario = MTransfer::SCENARIO_DEFAULT;
        $model->load($data, '');
        $model->status = MTransfer::STATUS_ISSUE;
        try {
            $transaction = Yii::$app->db->beginTransaction();
            static::trigger('_release', [$model]);

            if (!empty($data['details'])) {
                $transferDtls = ArrayHelper::index($model->transferDtls, 'id_product');
                static::trigger('_release_head', [$model]);
                foreach ($data['details'] as $dataDetail) {
                    $index = $dataDetail['id_product'];
                    $detail = $transferDtls[$index];
                    $detail->scenario = MTransfer::SCENARIO_RELEASE;
                    $detail->load($dataDetail, '');
                    $success = $success && $detail->save();
                    static::trigger('_release_body', [$model,$detail]);
                    $transferDtls[$index] = $detail;
                }
                $model->populateRelation('transferDtls', array_values($transferDtls));
                if ($success) {
                    static::trigger('_release_end', [$model]);
                }
            }
            if ($success && $model->save()) {
                static::trigger('_released', [$model]);
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

    /**
     * 
     * @param string $id
     * @param array $data
     * @param MTransfer $model
     * @return mixed
     * @throws \Exception
     */
    public static function receive($id, $data = [], $model = null)
    {
        $model = $model ? : static::findModel($id);

        $success = true;
        $model->scenario = MTransfer::SCENARIO_DEFAULT;
        $model->load($data, '');
        $model->status = MTransfer::STATUS_ISSUE;
        try {
            $transaction = Yii::$app->db->beginTransaction();
            static::trigger('_receive', [$model]);

            if (!empty($data['details'])) {
                $transferDtls = ArrayHelper::index($model->transferDtls, 'id_product');
                static::trigger('_receive_head', [$model]);
                foreach ($data['details'] as $dataDetail) {
                    $index = $dataDetail['id_product'];
                    if (isset($transferDtls[$index])) {
                        $detail = $transferDtls[$index];
                    } else {
                        $detail = new TransferDtl([
                            'id_transfer' => $model->id_transfer,
                            'id_product' => $index,
                            'id_uom' => $dataDetail['id_uom_receive']
                        ]);
                    }
                    $detail->scenario = MTransfer::SCENARIO_RECEIVE;
                    $detail->load($dataDetail, '');
                    $success = $success && $detail->save();
                    static::trigger('_receive_body', [$model,$detail]);
                    $transferDtls[$index] = $detail;
                }
                $model->populateRelation('transferDtls', array_values($transferDtls));
                if ($success) {
                    static::trigger('_receive_end', [$model]);
                }
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

    /**
     * 
     * @param string $id
     * @param array $data
     * @param MTransfer $model
     * @return mixed
     * @throws \Exception
     */
    public static function complete($id, $data = [], $model = null)
    {
        $model = $model ? : static::findModel($id);

        $success = true;
        $model->scenario = MTransfer::SCENARIO_DEFAULT;
        $model->load($data, '');
        $model->status = MTransfer::STATUS_RECEIVE;
        try {
            $transaction = Yii::$app->db->beginTransaction();
            static::trigger('_complete', [$model]);
            $transferDtls = ArrayHelper::index($model->transferDtls, 'id_product');
            if (!empty($data['details'])) {
                static::trigger('_complete_head', [$model]);
                foreach ($data['details'] as $dataDetail) {
                    $index = $dataDetail['id_product'];
                    $detail = $transferDtls[$index];
                    $detail->scenario = MTransfer::SCENARIO_COMPLETE;
                    $detail->load($dataDetail, '');
                    $success = $success && $detail->save();
                    static::trigger('_complete_body', [$model,$detail]);
                    $transferDtls[$index] = $detail;
                }
                $model->populateRelation('transferDtls', array_values($transferDtls));
                static::trigger('_complete_end', [$model]);
            }
            $complete = true;
            foreach ($transferDtls as $detail) {
                $complete = $complete && $detail->transfer_qty_send == $detail->transfer_qty_receive;
            }
            if (!$complete) {
                $model->addError('details', 'Not balance');
            }
            if ($success && $complete && $model->save()) {
                static::trigger('_completed', [$model]);
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