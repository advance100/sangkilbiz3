<?php

namespace core\base;

/**
 * Description of UnknownErrorException
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class UnknownErrorException extends \yii\base\Exception
{

    public function getName()
    {
        return 'Unknown Error';
    }
}
