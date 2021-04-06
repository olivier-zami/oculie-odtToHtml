<?php
namespace Oculie\Core;

class Exception extends \Exception
{
    const NO_EVENT_TRIGGERED_ON_GLOBAL_ROUTINE_CONTEXT = 1;

    private $task;

    public function __construct($message="", $code=0, $previous=NULL, $task=NULL)
    {
        $this->task = $task;
        parent::__construct($message, $code, $previous);
    }
}