<?php

namespace core\accounting\controllers;

/**
 * Description of GLController
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class GLController extends \core\base\rest\Controller
{
    /**
     * @inheritdoc
     */
    public $helperClass = 'core\accounting\components\GL';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['update'], $actions['delete']);

        return $actions;
    }
}