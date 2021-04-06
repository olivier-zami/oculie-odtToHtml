<?php
/**
 * Created by PhpStorm.
 * User: ozami
 * Date: 06/03/2020
 * Time: 11:00
 */

namespace Oculie\Core;


trait inquireMethods
{
    public function inquire($object)
    {
        $interface = NULL;
        if(is_subclass_of($object, Entity\Dynamic::class))
        var_dump(get_class($object));
    }
}