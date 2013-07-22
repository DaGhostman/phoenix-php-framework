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

namespace Phoenix\Application\Cache;

class APC {

    private static $_instance = null;
    
    private function __construct() {}

    public static function getInstance() {
        if (self::$_instance == null || !self::$_instance instanceof APC)
            self::$_instance = new APC ();
        
        return self::$_instance;
    }
    
    public function __get($name) {
        if (apc_exists($name)) {
            $result = apc_fetch($name);
        } elseif (!apc_exists($name)) {
            $result = false;
        }
        
        return $result;
    }

    public function __set($name, $value) {
        if (apc_exists($name)) {
            apc_delete($name);
            apc_add($name, $value, 3600);  
        } elseif (!apc_exists($name)) {
            apc_add($name, $value, 3600);
        }
    }

    public function __call($name, $args) {
        if (strpos($name, 'get') === 0):
            $field = substr($field, 3, strlen($field));
            return $this->$field;
        elseif (strpos($name, 'set') === 0):
            $field = substr($field, 3, strlen($field));
            return $this->$field = $args;
        endif;
    }
    
    public function get($key) {
        if (apc_exists($key))
            return apc_fetch($key);
        else 
            return false;
    }
    
    public function set($key, $val) {
        if (apc_exists($key)) {
            apc_delete ($key);
            return apc_store($key, $val, 3600);
        }
        else 
            return apc_store($key, $val, 3600);
    }
    
    public function exists($key) {
        return apc_exists($key);
    }

}

?>
