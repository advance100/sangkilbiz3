<?php

namespace core\base\rest;

use Yii;
use core\base\Api;
use yii\base\InvalidConfigException;
use yii\filters\auth\QueryParamAuth;

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
    public $serializer = 'core\base\rest\Serializer';

    /**
     *
     * @var Api 
     */
    public $api;

    /**
     *
     * @var array 
     */
    public $actions = [];

    /**
     *
     * @var array 
     */
    public $verbs = [];

    public function init()
    {
        if ($this->api === null) {
            throw new InvalidConfigException(get_class($this) . '::$api must be set.');
        }
        if (!$this->api instanceof Api) {
            $this->api = Yii::createObject($this->api);
        }
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['authMethods']['query_param'] = [
            'class' => QueryParamAuth::className(),
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $coreAction = [
            'index' => [
                'class' => 'core\base\rest\IndexAction',
            ],
            'view' => [
                'class' => 'core\base\rest\ViewAction',
            ],
            'create' => [
                'class' => 'core\base\rest\CreateAction',
            ],
            'update' => [
                'class' => 'core\base\rest\UpdateAction',
            ],
            'delete' => [
                'class' => 'core\base\rest\DeleteAction',
            ],
        ];

        return array_filter(array_merge($coreAction, $this->actions));
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        $coreVerbs = [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
        return array_filter(array_merge($coreVerbs, $this->verbs));
    }
}