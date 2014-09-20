<?php

namespace core\inventory\components;

use Yii;
use core\inventory\models\StockMovement as MStockMovement;
use yii\base\NotSupportedException;

/**
 * Description of StockMovement
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class StockMovement extends \core\base\Api
{

    /**
     * @inheritdoc
     */
    public static function modelClass()
    {
        return MStockMovement::className();
    }

    /**
     * @inheritdoc
     */
    public static function prefixEventName()
    {
        return 'e_stock-movement';
    }

    /**
     *
     * @param  array                                $data
     * @param  \core\inventory\models\StockMovement $model
     * @return \core\inventory\models\StockMovement
     */
    public static function create($data, $model = null)
    {
        /* @var $model MStockMovement */
        $model = $model ? : static::createNewModel();
        $success = false;
        $model->scenario = MStockMovement::SCENARIO_DEFAULT;
        $model->load($data, '');
        if (!empty($data['details'])) {
            static::trigger('_create', [$model]);
            $success = $model->save();
            $success = $model->saveRelated('stockMovementDtls', $data, $success, 'details');
            if ($success) {
                static::trigger('_created', [$model]);
            } else {
                if ($model->hasRelatedErrors('stockMovementDtls')) {
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
     * @throws NotSupportedException
     */
    public static function update($id, $data, $model = null)
    {
        throw new NotSupportedException();
    }

    /**
     *
     * @throws NotSupportedException
     */
    public static function delete($id, $model = null)
    {
        throw new NotSupportedException();
    }
}
