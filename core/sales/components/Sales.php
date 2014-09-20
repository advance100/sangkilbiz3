<?php

namespace core\sales\components;

use Yii;
use core\sales\models\Sales as MSales;
use yii\helpers\ArrayHelper;

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
     * @param  array                    $data
     * @param  \core\sales\models\Sales $model
     * @return \core\sales\models\Sales
     * @throws \Exception
     */
    public static function create($data, $model = null)
    {
        /* @var $model MSales */
        $model = $model ? : static::createNewModel();
        $success = false;
        $model->scenario = MSales::SCENARIO_DEFAULT;
        $model->load($data, '');
        static::trigger('_create', [$model]);
        if (!empty($post['details'])) {
            $success = $model->save();
            $success = $model->saveRelated('salesDtls', $data, $success, 'details');
            if ($success) {
                static::trigger('_created', [$model]);
            } else {
                if ($model->hasRelatedErrors('salesDtls')) {
                    $model->addError('details', 'Details validation error');
                }
            }
        } else {
            $model->validate();
            $model->addError('details', 'Details cannot be blank');
        }

        return static::processOutput($success, $model);
    }

    /**
     *
     * @param  string                   $id
     * @param  array                    $data
     * @param  \core\sales\models\Sales $model
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
            $success = $model->save();
            if (!empty($data['details'])) {
                $success = $model->saveRelated('salesDtls', $data, $success, 'details');
            }
            if ($success) {
                static::trigger('_updated', [$model]);
            } else {
                if ($model->hasRelatedErrors('salesDtls')) {
                    $model->addError('details', 'Details validation error');
                }
            }
        } else {
            $model->validate();
            $model->addError('details', 'Details cannot be blank');
        }

        return static::processOutput($success, $model);
    }

    /**
     *
     * @param  string                   $id
     * @param  array                    $data
     * @param  \core\sales\models\Sales $model
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
        static::trigger('_release', [$model]);
        $salesDtls = ArrayHelper::index($model->salesDtls, 'id_product');
        if (!empty($data['details'])) {
            static::trigger('_release_head', [$model]);
            foreach ($data['details'] as $dataDetail) {
                $index = $dataDetail['id_product'];
                $detail = $salesDtls[$index];
                $detail->scenario = MSales::SCENARIO_RELEASE;
                $detail->load($dataDetail, '');
                $success = $success && $detail->save();
                static::trigger('_release_body', [$model, $detail]);
                $salesDtls[$index] = $detail;
            }
            $model->populateRelation('salesDtls', array_values($salesDtls));
            static::trigger('_release_end', [$model]);
        }
        $allReleased = true;
        foreach ($salesDtls as $detail) {
            $allReleased = $allReleased && $detail->sales_qty == $detail->sales_qty_release;
        }
        if ($allReleased) {
            $model->status = MSales::STATUS_RELEASED;
        }
        if ($success && $model->save()) {
            static::trigger('_released', [$model]);
        } else {
            $success = false;
        }

        return static::processOutput($success, $model);
    }
}
