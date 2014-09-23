<?php

namespace rest\base;

use yii\base\InvalidConfigException;
use core\base\Api;

/**
 * Description of Action
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class Action extends \yii\base\Action
{
    /**
     *
     * @var Api 
     */
    public $helperClass;

    public function init()
    {
        if ($this->helperClass === null) {
            throw new InvalidConfigException(get_class($this) . '::$helperClass must be set.');
        }
    }
}