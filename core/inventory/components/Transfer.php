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
    /**
     *
     * @var string 
     */
    public $modelClass = 'core\inventory\models\Transfer';

    /**
     *
     * @var string 
     */
    public $prefixEventName = 'e_transfer';

    /**
     *
     * @param  array      $data
     * @param  type       $model
     * @return type
     * @throws \Exception
     */
    public function create($data, $model = null)
    {
        $model = $model ? : $this->createNewModel();
        $success = false;
        $model->scenario = MTransfer::SCENARIO_DEFAULT;
        $model->load($data, '');

        if (!empty($data['details'])) {
            $this->fire('_create', [$model]);
            $success = $model->save();
            $success = $model->saveRelated('transferDtls', $data, $success, 'details');
            if ($success) {
                $this->fire('_created', [$model]);
            } else {
                if ($model->hasRelatedErrors('transferDtls')) {
                    $model->addError('details', 'Details validation error');
                }
            }
        } else {
            $model->validate();
            $model->addError('details', 'Details cannot be blank');
        }

        return $this->processOutput($success, $model);
    }

    public function update($id, $data, $model = null)
    {
        $model = $model ? : $this->findModel($id);

        $success = false;
        $model->scenario = MTransfer::SCENARIO_DEFAULT;
        $model->load($data, '');

        if (!isset($data['details']) || $data['details'] !== []) {
            $this->fire('_update', [$model]);
            $success = $model->save();
            if (!empty($data['details'])) {
                $success = $model->saveRelated('transferDtls', $data, $success, 'details');
            }
            if ($success) {
                $this->fire('_updated', [$model]);
            } else {
                if ($model->hasRelatedErrors('transferDtls')) {
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
     * @param  string     $id
     * @param  array      $data
     * @param  MTransfer  $model
     * @return mixed
     * @throws \Exception
     */
    public function release($id, $data = [], $model = null)
    {
        $model = $model ? : $this->findModel($id);

        $success = true;
        $model->scenario = MTransfer::SCENARIO_DEFAULT;
        $model->load($data, '');
        $model->status = MTransfer::STATUS_ISSUE;
        $this->fire('_release', [$model]);

        if (!empty($data['details'])) {
            $transferDtls = ArrayHelper::index($model->transferDtls, 'id_product');
            $this->fire('_release_head', [$model]);
            foreach ($data['details'] as $dataDetail) {
                $index = $dataDetail['id_product'];
                $detail = $transferDtls[$index];
                $detail->scenario = MTransfer::SCENARIO_RELEASE;
                $detail->load($dataDetail, '');
                $success = $success && $detail->save();
                $this->fire('_release_body', [$model, $detail]);
                $transferDtls[$index] = $detail;
            }
            $model->populateRelation('transferDtls', array_values($transferDtls));
            if ($success) {
                $this->fire('_release_end', [$model]);
            }
        }
        if ($success && $model->save()) {
            $this->fire('_released', [$model]);
        } else {
            $success = false;
        }

        return $this->processOutput($success, $model);
    }

    /**
     *
     * @param  string     $id
     * @param  array      $data
     * @param  MTransfer  $model
     * @return mixed
     * @throws \Exception
     */
    public function receive($id, $data = [], $model = null)
    {
        $model = $model ? : $this->findModel($id);

        $success = true;
        $model->scenario = MTransfer::SCENARIO_DEFAULT;
        $model->load($data, '');
        $model->status = MTransfer::STATUS_ISSUE;
        $this->fire('_receive', [$model]);

        if (!empty($data['details'])) {
            $transferDtls = ArrayHelper::index($model->transferDtls, 'id_product');
            $this->fire('_receive_head', [$model]);
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
                $this->fire('_receive_body', [$model, $detail]);
                $transferDtls[$index] = $detail;
            }
            $model->populateRelation('transferDtls', array_values($transferDtls));
            if ($success) {
                $this->fire('_receive_end', [$model]);
            }
        }
        if ($success && $model->save()) {
            $this->fire('_received', [$model]);
        } else {
            $success = false;
        }

        return $this->processOutput($success, $model);
    }

    /**
     *
     * @param  string     $id
     * @param  array      $data
     * @param  MTransfer  $model
     * @return mixed
     * @throws \Exception
     */
    public function complete($id, $data = [], $model = null)
    {
        $model = $model ? : $this->findModel($id);

        $success = true;
        $model->scenario = MTransfer::SCENARIO_DEFAULT;
        $model->load($data, '');
        $model->status = MTransfer::STATUS_RECEIVE;
        $this->fire('_complete', [$model]);
        $transferDtls = ArrayHelper::index($model->transferDtls, 'id_product');
        if (!empty($data['details'])) {
            $this->fire('_complete_head', [$model]);
            foreach ($data['details'] as $dataDetail) {
                $index = $dataDetail['id_product'];
                $detail = $transferDtls[$index];
                $detail->scenario = MTransfer::SCENARIO_COMPLETE;
                $detail->load($dataDetail, '');
                $success = $success && $detail->save();
                $this->fire('_complete_body', [$model, $detail]);
                $transferDtls[$index] = $detail;
            }
            $model->populateRelation('transferDtls', array_values($transferDtls));
            $this->fire('_complete_end', [$model]);
        }
        $complete = true;
        foreach ($transferDtls as $detail) {
            $complete = $complete && $detail->transfer_qty_send == $detail->transfer_qty_receive;
        }
        if (!$complete) {
            $model->addError('details', 'Not balance');
        }
        if ($success && $complete && $model->save()) {
            $this->fire('_completed', [$model]);
        } else {
            $success = false;
        }

        return $this->processOutput($success, $model);
    }
}