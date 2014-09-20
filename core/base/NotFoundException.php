<?php

namespace core\base;

/**
 * Description of NotFoundException
 *
 * @author Misbahul D Munir (mdmunir) <misbahuldmunir@gmail.com>
 */
class NotFoundException extends \yii\base\Exception
{

    public function getName()
    {
        return 'Not Found';
    }
}
