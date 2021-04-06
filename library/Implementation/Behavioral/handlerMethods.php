<?php
/**
 * Created by PhpStorm.
 * User: ozami
 * Date: 25/02/2020
 * Time: 17:09
 */

namespace Oculie\Core\Implementation;

trait handlerMethods
{
    public function handle($object)
    {
        $this->instance = $object;
    }

    public function getInstance()
    {
        return $this->instance;
    }
}