<?php

/**
 * 
 * @author Dimitar Dimitrov <daghostman.dd@gmail.com>
 * @link http://web-forge.org/
 * @copyright (c) 2013, Dimitar Dimitrov
 * @license http://creativecommons.org/licenses/by-sa/3.0/ Attribution-ShareAlike 3.0 Unported
 * 
 */
namespace Phoenix\Core\Signal;


class Manager {

    protected $_registry = array();
    protected static $_instance = null;
    protected $_signals = null;
    
    private function __construct() {}
    
    public static function getInstance() {
        if (!self::$_instance instanceof Manager):
            self::$_instance = new Manager ();
        endif;
        
        return self::$_instance;
    }
    
    
    public function bind($signal, $callback)
    {
        
        if (!array_key_exists($signal, $this->_registry))
            $this->_registry[$signal] = array();
        $this->_registry[$signal][] = $callback;
        
        return $this;
    }
    
    public function unbind($signal, $context = null)
    {
        foreach ($this->_registry[$signal] as $key => $val):
            if ($context != null):
                if ($val == $context):
                    unset($this->_registry[$signal][$key]);
                endif;
            else:
                unset($this->_registry[$signal]);
            endif;
        endforeach;
        
        return $this;
    }
    
    public function emit($signal, $args = array()) {
        if (array_key_exists($signal, $this->_registry)):
            if (!empty($this->_registry[$signal])):
                foreach($this->_registry[$signal] as $val) :
                    $val($args);
                endforeach;
            endif;
        endif;
        
        return $this;
    }

}

?>
