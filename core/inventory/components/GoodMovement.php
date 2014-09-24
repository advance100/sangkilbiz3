<?php

namespace core\inventory\components;

use Yii;
use core\inventory\models\GoodMovement as MGoodMovement;
use yii\base\NotSupportedException;

/**
 * Description of GoodMovement
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class GoodMovement extends \core\base\Api
{

    /**
     * @inheritdoc
     */
    public static function modelClass()
    {
        return MGoodMovement::className();
    }

    /**
     * @inheritdoc
     */
    public static function prefixEventName()
    {
        return 'e_good-movement';
    }

    /**
     *
     * @param  array                                $data
     * @param  \core\inventory\models\GoodMovement $model
     * @return \core\inventory\models\GoodMovement
     */
    public static function create($data, $model = null)
    {
        /* @var $model MGoodMovement */
        $model = $model ? : static::createNewModel();
        $success = false;
        $model->scenario = MGoodMovement::SCENARIO_DEFAULT;
        $model->load($data, '');
        if (!empty($data['details'])) {
            static::trigger('_create', [$model]);
            $success = $model->save();
            $success = $model->saveRelated('goodMovementDtls', $data, $success, 'details');
            if ($success) {
                static::trigger('_created', [$model]);
            } else {
                if ($model->hasRelatedErrors('goodMovementDtls')) {
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
