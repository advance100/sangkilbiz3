<?php

namespace core\accounting\components;

use Yii;
use core\accounting\models\Payment as MPayment;
use core\accounting\models\PaymentDtl;
use core\accounting\models\Invoice;
use yii\base\UserException;

/**
 * Description of Payment
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class Payment extends \core\base\Api
{

    /**
     * @inheritdoc
     */
    public static function modelClass()
    {
        return MPayment::className();
    }

    /**
     * @inheritdoc
     */
    public static function prefixEventName()
    {
        return 'e_payment';
    }

    public static function create($data, $model = null)
    {
        $model = $model ? : static::createNewModel();
        $success = false;
        $model->scenario = MPayment::SCENARIO_DEFAULT;
        $model->load($data, '');
        if (!empty($data['details'])) {
            try {
                $transaction = Yii::$app->db->beginTransaction();
                static::trigger('_create', [$model]);
                $success = $model->save();
                $success = $model->saveRelated('paymentDtls', $data, $success, 'details');
                if ($success) {
                    static::trigger('_created', [$model]);
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                    if ($model->hasRelatedErrors('paymentDtls')) {
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

    public static function update($id, $data, $model = null)
    {
        $model = $model ? : static::findModel($id);
        $success = false;
        $model->scenario = MPayment::SCENARIO_DEFAULT;
        $model->load($data, '');
        if (!isset($data['details']) || $data['details'] !== []) {
            try {
                $transaction = Yii::$app->db->beginTransaction();
                static::trigger('_update', [$model]);
                $success = $model->save();
                if (!empty($data['details'])) {
                    $success = $model->saveRelated('paymentDtls', $data, $success, 'details');
                }
                if ($success) {
                    static::trigger('_updated', [$model]);
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                    if ($model->hasRelatedErrors('paymentDtls')) {
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

    public function createFromInvoice($data, $model = null)
    {
        $pay_vals = ArrayHelper::map($data['details'], 'id_invoice', 'value');
        $ids = array_keys($pay_vals);

        $invoice_values = Invoice::find()
            ->where(['id_invoice' => $ids])
            ->indexBy('id_invoice')
            ->asArray()
            ->all();

        $vendor = $inv_type = null;
        $vendors = $inv_types = [];
        foreach ($invoice_values as $row) {
            $vendor = $row['id_vendor'];
            $vendors[$vendor] = true;
            $inv_type = $row['invoice_type'];
            $inv_types[$inv_type] = true;
        }
        if (count($vendors) !== 1) {
            throw new UserException('Vendor harus sama');
        }
        if (count($inv_types) !== 1) {
            throw new UserException('Type invoice harus sama');
        }

        $invoice_paid = PaymentDtl::find()
            ->select(['id_invoice', 'total' => 'sum(payment_value)'])
            ->where(['id_invoice' => $ids])
            ->groupBy('id_invoice')
            ->indexBy('id_invoice')
            ->asArray()
            ->all();

        $data['id_vendor'] = $vendor;
        $data['payment_type'] = $inv_type;
        $details = [];
        foreach ($inv_vals as $id => $value) {
            $sisa = $invoice_values[$id]['invoice_value'];
            if (isset($invoice_paid[$id])) {
                $sisa -= $invoice_paid[$id]['total'];
            }
            if ($value > $sisa) {
                throw new UserException('Tagihan lebih besar dari sisa');
            }
            $details[] = [
                'id_invoice' => $id,
                'payment_value' => $value,
            ];
        }
        $data['details'] = $details;
        return static::processOutput($success, $model);
    }
    
    public static function post($id,$data,$model=null)
    {
        /* @var $model MPayment */
        $model = $model ? : static::findModel($id);
        $model->load($data,'');
        
        return static::processOutput($success, $model);
    }
}