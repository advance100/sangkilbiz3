<?php

namespace rest\base;

use Yii;

/**
 * Description of ViewAction
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class ViewAction extends Action
{

    public function run($id)
    {
        $helperClass = $this->helperClass;
        $model = $helperClass::findModel($id);
        $helperClass::trigger('_view', [$model]);
        return $model;
    }
}