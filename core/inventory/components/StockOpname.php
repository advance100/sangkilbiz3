<?php

namespace core\inventory\components;

use Yii;
use core\inventory\models\StockOpname as MStockOpname;
use core\inventory\models\StockOpnameDtl;
use yii\helpers\ArrayHelper;

/**
 * Description of StockOpname
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class StockOpname extends \core\base\Api
{

    public static function modelClass()
    {
        return MStockOpname::className();
    }

    public static function prefixEventName()
    {
        return 'e_stock-opname';
    }

    public static function create($data, $model = null)
    {
        $model = $model ? : static::createNewModel();
        $success = false;
        $model->scenario = MStockOpname::SCENARIO_DEFAULT;
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

    public static function append($id, $data, $model = null)
    {
        /* @var $model MStockOpname */
        $model = $model ? : static::findModel($id);
        $success = true;
        $model->scenario = MStockOpname::SCENARIO_DEFAULT;
        $model->load($data, '');
        try {
            $transaction = Yii::$app->db->beginTransaction();
            static::trigger('_append', [$model]);
            $stockOpnameDtls = ArrayHelper::index($model->stockOpnameDtls, 'id_product');
            foreach ($data['details'] as $dataDetail) {
                $index = $dataDetail['id_product']; // id_product
                if (isset($stockOpnameDtls[$index])) {
                    $detail = $stockOpnameDtls[$index];
                } else {
                    $detail = new StockOpnameDtl([
                        'id_opname' => $model->id_opname,
                        'id_product' => $dataDetail['id_product'],
                        'id_uom' => $dataDetail['id_uom'],
                        'qty' => 0
                    ]);
                }
                $detail->qty += $dataDetail['qty'];
                $success = $success && $detail->save();
                $stockOpnameDtls[$index] = $detail;
                static::trigger('_append_body', [$model,$detail]);
            }
            if ($success) {
                static::trigger('_appended', [$model]);
                $transaction->commit();
            } else {
                $transaction->rollBack();
            }
        } catch (\Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        }
        return static::processOutput($success, $model);
    }
}