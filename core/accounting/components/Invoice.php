<?php

namespace core\accounting\components;

use Yii;
use core\accounting\models\Invoice as MInvoice;
use core\accounting\models\InvoiceDtl;
use core\purchase\models\Purchase;
use core\sales\models\Sales;
use yii\base\UserException;
use core\inventory\models\GoodMovement;
use core\inventory\models\GoodMovementDtl;

/**
 * Description of Invoice
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class Invoice extends \core\base\Api
{
    /**
     *
     * @var string 
     */
    public $modelClass = 'core\accounting\models\Invoice';

    /**
     *
     * @var string 
     */
    public $prefixEventName = 'e_invoice';

    /**
     *
     * @param  array                           $data
     * @param  \core\accounting\models\Invoice $model
     * @return core\accounting\models\Invoice
     * @throws \Exception
     */
    public function create($data, $model = null)
    {
        /* @var $model MInvoice */
        $model = $model ? : $this->createNewModel();
        $success = false;
        $model->scenario = MInvoice::SCENARIO_DEFAULT;
        $model->status = MInvoice::STATUS_DRAFT;
        $model->load($data, '');
        if (!empty($data['details'])) {
            $total = 0;
            foreach ($data['details'] as $detail) {
                $total += $detail['trans_value'];
            }
            $model->invoice_value = $total;
            $this->fire('_create', [$model]);
            $success = $model->save();
            $success = $model->saveRelated('invoiveDtls', $data, $success, 'details');
            if ($success) {
                $this->fire('_created', [$model]);
            } else {
                if ($model->hasRelatedErrors('invoiveDtls')) {
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
     * @param  string                          $id
     * @param  array                           $data
     * @param  \core\accounting\models\Invoice $model
     * @return core\accounting\models\Invoice
     * @throws \Exception
     */
    public function update($id, $data, $model = null)
    {
        /* @var $model MInvoice */
        $model = $model ? : $this->findModel($id);
        $success = false;
        $model->scenario = MInvoice::SCENARIO_DEFAULT;
        $model->load($data, '');
        if (!isset($data['details']) || $data['details'] !== []) {
            $total = 0;
            foreach ($data['details'] as $detail) {
                $total += $detail['trans_value'];
            }
            $model->invoice_value = $total;
            $this->fire('_update', [$model]);
            $success = $model->save();
            if (!empty($data['details'])) {
                $success = $model->saveRelated('invoiveDtls', $data, $success, 'details');
            }
            if ($success) {
                $this->fire('_updated', [$model]);
            } else {
                if ($model->hasRelatedErrors('invoiveDtls')) {
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
     * @param  array                           $data
     * @param  \core\accounting\models\Invoice $model
     * @return core\accounting\models\Invoice
     * @throws UserException
     */
    public function createFromPurchase($data, $model = null)
    {
        $ids = (array) $data['id_purchase'];
        $vendors = Purchase::find()->select('id_supplier')
                ->distinct()->column();

        if (count($vendors) !== 1) {
            throw new UserException('Vendor harus sama');
        }
        // invoice for GR
        $received = GoodMovement::find()->select('id_movement')
                ->where([
                    'type_reff' => GoodMovement::TYPE_PURCHASE,
                    'id_reff' => $ids
                ])->column();
        $invoiced = InvoiceDtl::find()->select('id_reff')
                ->where([
                    'type_reff' => InvoiceDtl::TYPE_PURCHASE_GR,
                    'id_reff' => $received,
                ])->column();
        $new = array_diff($received, $invoiced);
        $values = GoodMovement::find()
                ->select(['hdr.id_movement', 'jml' => 'sum(dtl.qty*dtl.trans_value)'])
                ->from(GoodMovement::tableName() . ' hdr')
                ->joinWith(['goodMovementDtls' => function($q) {
                    $q->from(GoodMovementDtl::tableName() . ' dtl');
                }])
                ->andWhere([
                    'hdr.type_reff' => GoodMovement::TYPE_PURCHASE,
                    'hdr.id_reff' => $new
                ])
                ->groupBy('hdr.id_movement')
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
        $model = $this->create($data, $model);
        $model = $this->post('', [], $model);

        return $model;
    }

    /**
     * @param  array                           $data
     * @param  \core\accounting\models\Invoice $model
     * @return \core\accounting\models\Invoice
     * @throws UserException
     */
    public function createFromSales($data, $model = null)
    {
        $ids = (array) $data['id_sales'];
        $vendors = Sales::find()->select('id_customer')
                ->distinct()->column();

        if (count($vendors) !== 1) {
            throw new UserException('Vendor harus sama');
        }
        // invoice for GI
        $released = GoodMovement::find()->select('id_movement')
                ->where([
                    'type_reff' => GoodMovement::TYPE_SALES,
                    'id_reff' => $ids
                ])->column();
        $invoiced = InvoiceDtl::find()->select('id_reff')
                ->where([
                    'type_reff' => InvoiceDtl::TYPE_SALES_GI,
                    'id_reff' => $released,
                ])->column();
        $new = array_diff($released, $invoiced);
        $values = GoodMovement::find()
                ->select(['hdr.id_movement', 'jml' => 'sum(dtl.qty*dtl.trans_value)'])
                ->from(GoodMovement::tableName() . ' hdr')
                ->joinWith(['goodMovementDtls' => function($q) {
                    $q->from(GoodMovementDtl::tableName() . ' dtl');
                }])
                ->where([
                    'hdr.type_reff' => GoodMovement::TYPE_SALES,
                    'hdr.id_reff' => $new
                ])
                ->groupBy('hdr.id_movement')
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
        $model = $this->create($data, $model);
        $model = $this->post('', [], $model);

        return $model;
    }

    public static function post($id, $data, $model = null)
    {
        /* @var $model MInvoice */
        $model = $model ? : $this->findModel($id);
        $success = false;
        $model->scenario = MInvoice::SCENARIO_DEFAULT;
        $model->load($data, '');
        $model->status = MInvoice::STATUS_POSTED;
        $this->fire('_post', [$model]);
        $success = $model->save();
        if ($success) {
            $this->fire('_posted', [$model]);
        } else {
            $success = false;
        }

        return $this->processOutput($success, $model);
    }
}