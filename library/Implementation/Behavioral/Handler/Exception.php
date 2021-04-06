<?php
namespace Oculie\Core\Handler;

class Exception
{
    private $exception;

    public function handle($exception)
    {
        $this->exception = $exception;
        \Oculie\Debug::dump($exception);
        return $this;
    }

    public function getResponse()
    {

    }
}