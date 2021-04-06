<?php
namespace Oculie;

class Input
{
    private static $instance;
    
    public $__construct = NULL;
    
    public static function instantiate()
    {
        $args = func_get_args();
        self::$instance = new \Oculie\Input();
        self::$instance->__construct = $args;
        return self::$instance;
    }
    
    public function __construct()
    {
        $this->__construct = func_get_args();
    }
    
    public function test()//TODO: creer un trait permettant setter les champs d'un object
    {
        return self::$instance;
    }
}
?>
