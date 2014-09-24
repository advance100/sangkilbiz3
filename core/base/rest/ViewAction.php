<?php

namespace core\base\rest;

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
        $model = $this->api->findModel($id);
        $this->api->fire('_view', [$model]);
        return $model;
    }
}