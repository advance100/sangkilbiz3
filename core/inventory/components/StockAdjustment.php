<?php

namespace core\inventory\components;

use Yii;
use core\inventory\models\StockAdjustment as MStockAdjustment;
use core\inventory\models\StockOpname;
use core\master\models\ProductStock;
use core\master\models\ProductUom;

/**
 * Description of StockAdjusment
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class StockAdjustment extends \core\base\Api
{

    public static function modelClass()
    {
        return MStockAdjustment::className();
    }

    public static function prefixEventName()
    {
        return 'e_stock-adjustment';
    }

    /**
     * Create stock adjustment.
     * 
     * @param array $data
     * @param \core\inventory\models\StockAdjustment $model
     * @return \core\inventory\models\StockAdjustment
     * @throws \Exception
     */
    public static function create($data, $model = null)
    {
        /* @var $model MStockAdjustment */
        $model = $model ? : new MStockAdjustment();
        $success = false;
        $model->scenario = MStockAdjustment::SCENARIO_DEFAULT;
        $model->load($data, '');
        if (!empty($data['details'])) {
            try {
                $transaction = Yii::$app->db->beginTransaction();
                static::trigger('_create', [$model]);
                $success = $model->save();
                $success = $model->saveRelated('stockAdjustmentDtls', $data, $success, 'details');
                if ($success) {
                    static::trigger('_created', [$model]);
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                    if ($model->hasRelatedErrors('stockAdjustmentDtls')) {
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
     * Update stock adjustment.
     * 
     * @param string $id
     * @param array $data
     * @param \core\inventory\models\StockAdjustment $model
     * @return \core\inventory\models\StockAdjustment
     * @throws \Exception
     */
    public static function update($id, $data, $model = null)
    {
        /* @var $model MStockAdjustment */
        $model = $model ? : static::findModel($id);
        $success = false;
        $model->scenario = MStockAdjustment::SCENARIO_DEFAULT;
        $model->load($data, '');
        if (!isset($data['details']) || $data['details'] !== []) {
            try {
                $transaction = Yii::$app->db->beginTransaction();
                static::trigger('_update', [$model]);
                $success = $model->save();
                if (!empty($data['details'])) {
                    $success = $model->saveRelated('stockAdjustmentDtls', $data, $success, 'details', MStockAdjustment::SCENARIO_DEFAULT);
                }
                if ($success) {
                    static::trigger('_updated', [$model]);
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                    if ($model->hasRelatedErrors('stockAdjustmentDtls')) {
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
     * Apply stock adjustment
     * 
     * @param string $id
     * @param array $data
     * @param \core\inventory\models\StockAdjustment $model
     * @return \core\inventory\models\StockAdjustment
     * @throws \Exception
     */
    public static function apply($id, $data = [], $model = null)
    {
        /* @var $model MStockAdjustment */
        $model = $model ? : static::findModel($id);
        $success = false;
        $model->scenario = MStockAdjustment::SCENARIO_DEFAULT;
        $model->load($data, '');
        $model->status = MStockAdjustment::STATUS_APPLIED;
        try {
            $transaction = Yii::$app->db->beginTransaction();
            static::trigger('_apply', [$model]);
            $success = $model->save();
            if ($success) {
                static::trigger('_applied', [$model]);
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
     * @param StockOpname $opname
     * @param MStockAdjustment $model
     * @return mixed
     * @throws \Exception
     */
    public static function createFromOpname($opname, $model = null)
    {
        // info product
        $currentStocks = ProductStock::find()->select(['id_product', 'qty_stock'])
                ->where(['id_warehouse' => $opname->id_warehouse])
                ->indexBy('id_product')->asArray()->all();
        $isiProductUoms = [];
        foreach (ProductUom::find()->asArray()->all() as $row) {
            $isiProductUoms[$row['id_product']][$row['id_uom']] = $row['isi'];
        }
        // ***

        $data = [
            'id_warehouse' => $opname->id_warehouse,
            'adjustment_date' => date('Y-m-d'),
            'id_reff' => $opname->id_opname,
            'description' => "Stock adjustment from stock opname no \"{$opname->opname_num}\"."
        ];
        $details = [];
        foreach ($opname->stockOpnameDtls as $detail) {
            $cQty = $currentStocks[$detail->id_product] / $isiProductUoms[$detail->id_product][$detail->id_uom];
            $details[] = [
                'id_product' => $detail->id_product,
                'id_uom' => $detail->id_uom,
                'qty' => $detail->qty - $cQty,
            ];
        }
        $data['details'] = $details;
        return static::create($data, $model);
    }
}