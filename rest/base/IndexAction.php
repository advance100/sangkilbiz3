<?php

namespace rest\base;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * Description of ViewAction
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class IndexAction extends Action
{
    public $prepareDataProvider;
    
    public function run()
    {
        $helperClass = $this->helperClass;
        $helperClass::trigger('_list');
        return $this->prepareDataProvider();
    }

    /**
     * Prepares the data provider that should return the requested collection of the models.
     * @return ActiveDataProvider
     */
    protected function prepareDataProvider()
    {
        if ($this->prepareDataProvider !== null) {
            return call_user_func($this->prepareDataProvider, $this);
        }

        /* @var $modelClass \yii\db\BaseActiveRecord */
        $helperClass = $this->helperClass;
        $modelClass = $helperClass::modelClass();
        return new ActiveDataProvider([
            'query' => $modelClass::find()
        ]);
    }    
}