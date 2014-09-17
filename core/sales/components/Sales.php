<?php

namespace core\sales\components;

use core\sales\models\Sales as MSales;

/**
 * Description of Sales
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class Sales extends \core\base\Api
{

    /**
     * @inheritdoc
     */
    public static function modelClass()
    {
        return MSales::className();
    }

    /**
     * @inheritdoc
     */
    public static function prefixEventName()
    {
        return 'e_sales';
    }
    
    /**
     * 
     * @param array $data
     * @param \core\sales\models\Sales $model
     * @return \core\sales\models\Sales
     * @throws \Exception
     */
    public static function create($data, $model = null)
    {
        /* @var $model MSales */
        $model = $model ? : new MSales();
        $success = false;
        $model->scenario = MSales::SCENARIO_DEFAULT;
        $model->load($data, '');
        static::trigger('_create', [$model]);
        if (!empty($post['details'])) {
            try {
                $transaction = Yii::$app->db->beginTransaction();
                $success = $model->save();
                $success = $model->saveRelated('salesDtls', $data, $success, 'details');
                if ($success) {
                    static::trigger('_created', [$model]);
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                    if ($model->hasRelatedErrors('salesDtls')) {
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
     * @param \core\sales\models\Sales $model
     * @return \core\sales\models\Sales
     * @throws \Exception
     */
    public static function update($id, $data, $model = null)
    {
        $model = $model ? : static::findModel($id);

        $success = false;
        $model->scenario = MSales::SCENARIO_DEFAULT;
        $model->load($data, '');
        static::trigger('_update', [$model]);

        if (!isset($data['details']) || $data['details'] !== []) {
            try {
                $transaction = Yii::$app->db->beginTransaction();
                $success = $model->save();
                if (!empty($data['details'])) {
                    $success = $model->saveRelated('salesDtls', $data, $success, 'details');
                }
                if ($success) {
                    static::trigger('_updated', [$model]);
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                    if ($model->hasRelatedErrors('salesDtls')) {
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
     * @param \core\sales\models\Sales $model
     * @return mixed
     * @throws \Exception
     */
    public static function release($id, $data = [], $model = null)
    {
        $model = $model ? : static::findModel($id);

        $success = true;
        $model->scenario = MSales::SCENARIO_DEFAULT;
        $model->load($data, '');
        $model->status = MSales::STATUS_RELEASE;
        try {
            $transaction = Yii::$app->db->beginTransaction();
            static::trigger('_release', [$model]);
            $salesDtls = $model->salesDtls;
            if (!empty($data['details'])) {
                static::trigger('_release_head', [$model]);
                foreach ($data['details'] as $index => $dataDetail) {
                    $detail = $salesDtls[$index];
                    $detail->scenario = MSales::SCENARIO_RELEASE;
                    $detail->load($dataDetail, '');
                    $success = $success && $detail->save();
                    static::trigger('_release_body', [$model,$detail]);
                    $salesDtls[$index] = $detail;
                }
                $model->populateRelation('salesDtls', $salesDtls);
                static::trigger('_release_end', [$model]);
            }
            $allReleased = true;
            foreach ($salesDtls as $detail) {
                $allReleased = $allReleased && $detail->sales_qty == $detail->sales_qty_release;
            }
            if($allReleased){
                $model->status = MSales::STATUS_RELEASED;
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
}