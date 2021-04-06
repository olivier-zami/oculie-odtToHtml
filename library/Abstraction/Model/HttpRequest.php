<?php
/**
 * Created by PhpStorm.
 * User: ozami
 * Date: 13/02/2020
 * Time: 16:25
 */

namespace Oculie\Resource\Entity;

class HttpRequest
{
    use \Oculie\Core\Implementation\dtoAccessorMethods;
    
    protected $path;
    protected $parameters;
}