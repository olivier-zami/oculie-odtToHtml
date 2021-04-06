<?php
/**
 * Created by PhpStorm.
 * User: ozami
 * Date: 18/02/2020
 * Time: 15:51
 */

namespace Oculie;

function getHttpRequest()
{
    return \Oculie\Core\Builder\HttpRequest::create()->getInstance();
}