<?php

namespace rest\base;

use core\base\Api;
use yii\base\InvalidConfigException;

/**
 * Description of RestController
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class Controller extends \yii\rest\Controller
{
    /**
     * @var string|array the configuration for creating the serializer that formats the response data.
     */
    public $serializer = 'rest\base\Serializer';

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

    public function actions()
    {
        $helperClass = $this->helperClass;

        return[
            'index' => [
                'class' => 'rest\base\IndexAction',
                'helperClass' => $helperClass,
            ],
            'view' => [
                'class' => 'rest\base\ViewAction',
                'helperClass' => $helperClass,
            ],
            'create' => [
                'class' => 'rest\base\CreateAction',
                'helperClass' => $helperClass,
            ],
            'update' => [
                'class' => 'rest\base\UpdateAction',
                'helperClass' => $helperClass,
            ],
            'delete' => [
                'class' => 'rest\base\DeleteAction',
                'helperClass' => $helperClass,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }
}