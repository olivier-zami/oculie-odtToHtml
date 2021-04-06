<?php
namespace Oculie;

class Task
{
    public $_id;
    public $_method;
    public $_output;
    public $_object;
    public $_parameters;
    
    
    public function __construct($object, $method, $parameters=NULL)//TODO: ajouter un parametere pour differencier les appels statiques et dynamiques
    {
        $this->_object = $object;
        $this->_method = $method;
        $this->_parameters = $parameters;
    }
    
    public function _run()
    {
        $this->_object = is_object($this->_object) ? $this->_object : new $this->_object();
        $this->_output = call_user_func_array(array($this->_object, $this->_method), $this->_parameters->__construct);//NOTE: $this->parametre est un objet de type
    }
}
?>
