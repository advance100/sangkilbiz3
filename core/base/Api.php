<?php

namespace core\base;

use Yii;
use yii\db\ActiveRecord;

/**
 * Api is base class for API.
 *
 * Api implements commonly fiture for crud.
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class Api
{
    /**
     * @var array
     */
    private static $_modelClasses = [];

    /**
     * @var array
     */
    private static $_prefixEventNames = [];

    /**
     * Returns the fully qualified name of this class.
     * @return string the fully qualified name of this class.
     */
    public static function className()
    {
        return get_called_class();
    }

    /**
     * Declares the class name of model associated with this API class.
     * By default this method returns the class name and replace word "components"
     * with word "models". You may override this method
     * if the class name is not named after this convention.
     * @return string the class name
     */
    public static function modelClass()
    {
        $class = static::className();
        if (!isset(static::$_modelClasses[$class])) {
            static::$_modelClasses[$class] = str_replace('components', 'models', $class);
        }

        return static::$_modelClasses[$class];
    }

    /**
     * @return string prefix event name
     */
    public static function prefixEventName()
    {
        $class = static::className();
        if (!isset(static::$_prefixEventNames[$class])) {
            if (($pos = strrpos($class, '\\')) !== false) {
                $class = substr($class, $pos);
            }
            static::$_prefixEventNames[$class] = 'e_' . Inflector::camel2id($class);
        }

        return static::$_prefixEventNames[$class];
    }

    /**
     *
     * @param boolean      $success
     * @param ActiveRecord $model
     *
     * @return ActiveRecord
     * @throws UnknownErrorException
     */
    protected static function processOutput($success, $model)
    {
        if (!$success && !$model->hasErrors()) {
            throw new UnknownErrorException('Error with unknown reason.');
        }

        return $model;
    }

    /**
     *
     * @param array        $data
     * @param ActiveRecord $model
     *
     * @return ActiveRecord
     */
    public static function create($data, $model = null)
    {
        /* @var $model ActiveRecord */
        $model = $model ? : static::createNewModel();
        static::trigger('_create', [$model]);
        $model->load($data, '');
        if ($model->save()) {
            static::trigger('_created', [$model]);

            return $model;
        } else {
            return static::processOutput(false, $model);
        }
    }

    /**
     *
     * @param  string       $id
     * @param  array        $data
     * @param  ActiveRecord $model
     * @return ActiveRecord
     */
    public static function update($id, $data, $model = null)
    {
        /* @var $model ActiveRecord */
        $model = $model ? : static::findModel($id);
        static::trigger('_update', [$model]);
        $model->load($data, '');
        if ($model->save()) {
            static::trigger('_updated', [$model]);

            return $model;
        } else {
            return static::processOutput(false, $model);
        }
    }

    /**
     *
     * @param  string       $id
     * @param  ActiveRecord $model
     * @return boolean
     */
    public static function delete($id, $model = null)
    {
        /* @var $model \yii\db\ActiveRecord */
        $model = $model ? : static::findModel($id);
        static::trigger('_delete', [$model]);
        if ($model->delete() !== false) {
            static::trigger('_deleted', [$model]);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Create model
     * @return ActiveRecord
     */
    public static function createNewModel()
    {
        return Yii::createObject(static::modelClass());
    }
    /**
     * Returns the data model based on the primary key given.
     * If the data model is not found, a 404 HTTP exception will be raised.
     * @param  string            $id             the ID of the model to be loaded. If the model has a composite primary key,
     *                                           the ID must be a string of the primary key values separated by commas.
     *                                           The order of the primary key values should follow that returned by the `primaryKey()` method
     *                                           of the model.
     * @param  boolean           $throwException
     * @return ActiveRecord      the model found
     * @throws NotFoundException if the model cannot be found
     */
    public static function findModel($id, $throwException = true)
    {
        /* @var $modelClass ActiveRecord */
        $modelClass = static::modelClass();
        $keys = $modelClass::primaryKey();
        if (count($keys) > 1) {
            $values = explode(',', $id);
            if (count($keys) === count($values)) {
                $model = $modelClass::findOne(array_combine($keys, $values));
            }
        } elseif ($id !== null) {
            $model = $modelClass::findOne($id);
        }

        if (isset($model)) {
            static::trigger('_find', [$model]);

            return $model;
        } elseif ($throwException) {
            throw new NotFoundException("Object not found: $id");
        }

        return null;
    }

    /**
     * Trigger event to `Yii::$app`.
     * @param string $name
     * @param array  $params
     */
    public static function trigger($name, $params = [])
    {
        Yii::$app->trigger(static::prefixEventName() . $name, new Event($params));
    }
}
