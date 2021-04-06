<?php
/**
 * Created by PhpStorm.
 * User: ozami
 * Date: 14/02/2020
 * Time: 16:41
 */

namespace Oculie\Resource\Entity;

class Event
{
    use \Oculie\Core\Implementation\dtoAccessorMethods;

    protected $name;
    protected $trigger;
}