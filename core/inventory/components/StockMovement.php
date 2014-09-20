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

    public static function modelClass()
    {
        return MStockMovement::className();
    }

    public static function prefixEventName()
    {
        return 'e_stock-movement';
    }

    public static function create($data, $model = null)
    {
        $model = $model ? : static::createNewModel();
        $success = false;
        $model->scenario = MStockMovement::SCENARIO_DEFAULT;
        $model->load($data, '');
        if (!empty($data['details'])) {
            try {
                $transaction = Yii::$app->db->beginTransaction();
                static::trigger('_create', [$model]);
                $success = $model->save();
                $success = $model->saveRelated('stockMovementDtls', $data, $success, 'details');
                if ($success) {
                    static::trigger('_created', [$model]);
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                    if ($model->hasRelatedErrors('stockMovementDtls')) {
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