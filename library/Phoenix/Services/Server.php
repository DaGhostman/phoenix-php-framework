<?php
namespace Phoenix\Services\Xmlrpc;

class Server {
    protected $_instance = null;
    protected $_methods = array();
    protected $response = null;
    
    public function __construct(){}
    
    final public function addMethod ($name, $callback)
    {
        $this->_methods[$name] = $callback;
    }
    
    final public function callMethod (string $name, array $params)
    {
        return $this->_methods[$name]($params);
    }
    
    public function __call($method, $args = array())
    {
        if (array_key_exists($method, $this->_methods)) 
            return $this->_methods[$method]($args);
        else
            return false;
    }
    
}
?>
