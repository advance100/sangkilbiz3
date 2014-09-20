<?php

namespace core\accounting\components;

use Yii;
use core\accounting\models\Invoice as MInvoice;
use core\accounting\models\InvoiceDtl;
use core\purchase\models\Purchase;
use core\sales\models\Sales;
use yii\base\UserException;
use core\inventory\models\StockMovement;

/**
 * Description of Invoice
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class Invoice extends \core\base\Api
{

    /**
     * @inheritdoc
     */
    public static function modelClass()
    {
        return MInvoice::className();
    }

    /**
     * @inheritdoc
     */
    public static function prefixEventName()
    {
        return 'e_invoice';
    }

    /**
     * 
     * @param array $data
     * @param \core\accounting\models\Invoice $model
     * @return core\accounting\models\Invoice
     * @throws \Exception
     */
    public static function create($data, $model = null)
    {
        /* @var $model MInvoice */
        $model = $model ? : static::createNewModel();
        $success = false;
        $model->scenario = MInvoice::SCENARIO_DEFAULT;
        $model->status = MInvoice::STATUS_DRAFT;
        $model->load($data, '');
        if (!empty($data['details'])) {
            try {
                $transaction = Yii::$app->db->beginTransaction();
                $total = 0;
                foreach ($data['details'] as $detail) {
                    $total += $detail['trans_value'];
                }
                $model->invoice_value = $total;
                static::trigger('_create', [$model]);
                $success = $model->save();
                $success = $model->saveRelated('invoiveDtls', $data, $success, 'details');
                if ($success) {
                    static::trigger('_created', [$model]);
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                    if ($model->hasRelatedErrors('invoiveDtls')) {
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
     * @param \core\accounting\models\Invoice $model
     * @return core\accounting\models\Invoice
     * @throws \Exception
     */
    public static function update($id, $data, $model = null)
    {
        /* @var $model MInvoice */
        $model = $model ? : static::findModel($id);
        $success = false;
        $model->scenario = MInvoice::SCENARIO_DEFAULT;
        $model->load($data, '');
        if (!isset($data['details']) || $data['details'] !== []) {
            try {
                $transaction = Yii::$app->db->beginTransaction();
                $total = 0;
                foreach ($data['details'] as $detail) {
                    $total += $detail['trans_value'];
                }
                $model->invoice_value = $total;
                static::trigger('_update', [$model]);
                $success = $model->save();
                if (!empty($data['details'])) {
                    $success = $model->saveRelated('invoiveDtls', $data, $success, 'details');
                }
                if ($success) {
                    static::trigger('_updated', [$model]);
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                    if ($model->hasRelatedErrors('invoiveDtls')) {
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
        return [$success, $model];
    }

    /**
     * 
     * @param array $data
     * @param \core\accounting\models\Invoice $model
     * @return core\accounting\models\Invoice
     * @throws UserException
     */
    public static function createFromPurchase($data, $model = null)
    {
        $ids = (array) $data['id_purchase'];
        $vendors = Purchase::find()->select('id_supplier')
                ->distinct()->column();

        if (count($vendors) !== 1) {
            throw new UserException('Vendor harus sama');
        }
        // invoice for GR
        $received = StockMovement::find()->select('id_movement')
                ->where([
                    'type_reff' => StockMovement::TYPE_PURCHASE,
                    'id_reff' => $ids
                ])->column();
        $invoiced = InvoiceDtl::find()->select('id_reff')
                ->where([
                    'type_reff' => InvoiceDtl::TYPE_PURCHASE_GR,
                    'id_reff' => $received,
                ])->column();
        $new = array_diff($received, $invoiced);
        $values = StockMovement::find()
                ->select(['{{%stock_movement}}.id_movement', 'jml' => 'sum(qty*trans_value)'])
                ->joinWith('stockMovementDtls')
                ->andWhere([
                    '{{%stock_movement}}.type_reff' => StockMovement::TYPE_PURCHASE,
                    '{{%stock_movement}}.id_reff' => $new
                ])
                ->groupBy('{{%stock_movement}}.id_movement')
                ->indexBy('id_movement')
                ->asArray()->all();

        unset($data['id_purchase']);
        $data['id_vendor'] = reset($vendors);
        $data['invoice_type'] = MInvoice::TYPE_IN;
        $details = [];
        foreach ($new as $id) {
            $details[] = [
                'type_reff' => InvoiceDtl::TYPE_PURCHASE_GR,
                'id_reff' => $id,
                'trans_value' => $values[$id]['jml']
            ];
        }
        // Invoice for Global discount
        // get complete received purchase that invoiced yet :D
        $completed = Purchase::find()->select(['id_purchase', 'discount'])
            ->andWhere(['status' => Purchase::STATUS_RECEIVED, 'id_purchase' => $ids])
            ->andWhere(['<>', 'discount', null])
            ->asArray()->indexBy('id_purchase')
            ->all();
        $invoiced = InvoiceDtl::find()->select('id_reff')
                ->where([
                    'type_reff' => InvoiceDtl::TYPE_PURCHASE_DISCOUNT,
                    'id_reff' => array_keys($completed),
                ])->column();
        $new = array_diff(array_keys($completed), $invoiced);
        foreach ($new as $id) {
            $details[] = [
                'type_reff' => InvoiceDtl::TYPE_PURCHASE_DISCOUNT,
                'id_reff' => $id,
                'trans_value' => -$completed['discount']
            ];
        }

        $data['details'] = $details;
        try {
            $transaction = Yii::$app->db->beginTransaction();
            $model = static::create($data, $model);
            $model = static::post('', [], $model);
            $transaction->commit();
            return $model;
        } catch (\Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        }
    }

    /**
     * @param array $data
     * @param \core\accounting\models\Invoice $model
     * @return \core\accounting\models\Invoice
     * @throws UserException
     */
    public static function createFromSales($data, $model = null)
    {
        $ids = (array) $data['id_sales'];
        $vendors = Sales::find()->select('id_customer')
                ->distinct()->column();

        if (count($vendors) !== 1) {
            throw new UserException('Vendor harus sama');
        }
        // invoice for GI
        $released = StockMovement::find()->select('id_movement')
                ->where([
                    'type_reff' => StockMovement::TYPE_SALES,
                    'id_reff' => $ids
                ])->column();
        $invoiced = InvoiceDtl::find()->select('id_reff')
                ->where([
                    'type_reff' => InvoiceDtl::TYPE_SALES_GI,
                    'id_reff' => $released,
                ])->column();
        $new = array_diff($released, $invoiced);
        $values = StockMovement::find()
                ->select(['{{%stock_movement}}.id_movement', 'jml' => 'sum(qty*trans_value)'])
                ->joinWith('stockMovementDtls')
                ->where([
                    '{{%stock_movement}}.type_reff' => StockMovement::TYPE_SALES,
                    '{{%stock_movement}}.id_reff' => $new
                ])
                ->groupBy('{{%stock_movement}}.id_movement')
                ->indexBy('id_movement')
                ->asArray()->all();

        unset($data['id_sales']);
        $data['id_vendor'] = reset($vendors);
        $data['invoice_type'] = MInvoice::TYPE_OUT;
        $details = [];
        foreach ($new as $id) {
            $details[] = [
                'type_reff' => InvoiceDtl::TYPE_SALES_GI,
                'id_reff' => $id,
                'trans_value' => $values[$id]['jml']
            ];
        }

        // Invoice for discount
        $completed = Sales::find()->select(['id_sales', 'discount'])
            ->andWhere(['status' => Sales::STATUS_RELEASED, 'id_sales' => $ids])
            ->andWhere(['<>', 'discount', null])
            ->asArray()->indexBy('id_sales')
            ->all();
        $invoiced = InvoiceDtl::find()->select('id_reff')
                ->where([
                    'type_reff' => InvoiceDtl::TYPE_SALES_DISCOUNT,
                    'id_reff' => array_keys($completed),
                ])->column();
        $new = array_diff(array_keys($completed), $invoiced);
        foreach ($new as $id) {
            $details[] = [
                'type_reff' => InvoiceDtl::TYPE_SALES_DISCOUNT,
                'id_reff' => $id,
                'trans_value' => -$completed['discount']
            ];
        }

        $data['details'] = $details;
        try {
            $transaction = Yii::$app->db->beginTransaction();
            $model = static::create($data, $model);
            $model = static::post('', [], $model);
            $transaction->commit();
            return $model;
        } catch (\Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        }
    }

    public static function post($id, $data, $model = null)
    {
        /* @var $model MInvoice */
        $model = $model ? : static::findModel($id);
        $success = false;
        $model->scenario = MInvoice::SCENARIO_DEFAULT;
        $model->load($data, '');
        $model->status = MInvoice::STATUS_POSTED;
        try {
            $transaction = Yii::$app->db->beginTransaction();
            static::trigger('_post', [$model]);
            $success = $model->save();
            if ($success) {
                static::trigger('_posted', [$model]);
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