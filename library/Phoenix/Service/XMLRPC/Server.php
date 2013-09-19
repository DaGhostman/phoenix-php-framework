<?php

/**
 * 
 * @author Dimitar Dimitrov <daghostman.dd@gmail.com>
 * @link http://web-forge.org/
 * @copyright (c) 2013, Dimitar Dimitrov
 * @license  GNU GPLv3
 * Phoenix PHP Framework - Another MVC framework
 *   Copyright (C) 2013  Dimitar Dimitrov
 *
 * 
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */
namespace Phoenix\Service\XMLRPC;
use Phoenix\Cache\Cacher;
use Phoenix\Cache\Cacheable;
use Phoenix\Application\Exception\Argument;
use Phoenix\Application\Exception\Runtime;

class Server {

    private static $_instance = null;
    protected $_methodPool = array();
    
    
    private function __construct() {
        $methodPool = &$this->_methodPool;
    	$this->addMethod('system::listMethods', function() use ($methodPool){
    		return array_values($methodPool);
    	});
    }
    
    /**
     * @method getInstance
     * @static
     * @access public
     * @return Server Returns instance of the server.
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new Server();
        }
        
        return self::$_instance;
    }
    
    public function listen()
    {
        $request = xmlrpc_decode_request(file_get_contents("php://input"), $method);
        if (array_key_exists($method, $this->_methodPool))
        {
            $this->result = $this->_methodPool[$method]($request);
        } else {
            $obj = new \stdClass();
            $obj->faultCode=4;
            $obj->faultString="Call to invalid method";
            print xmlrpc_encode_request(null, $obj);
            
            throw new Runtime("Call to unidentified method {$method}");
        }
        
        return xmlrpc_encode_request(null, $this->result);
        
    }
    public function addMethod($name, $callback)
    {
        if (is_callable($callback))
        {
            $this->_methodPool[$name] = $callback;
        } else {
            throw new Argument("The supplied method is not callable. Please make sure that the method is passed correctly");
        }
        
        return $this;
    }
    
    public function removeMethod($name)
    {
        if (array_key_exists($name, $this->_methodPool)) {
            unset($this->_methodPool[$name]);
        } else {
             throw new Argument("The supplied callable is not found in the methods pool");
        }
    }
    
    
    

}

?>
