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
namespace Phoenix\Cache;
use Phoenix\Cache\Cacheable;

class Output implements Cacheable {

    static protected $_instance = null;
    protected $output, $buffer = array();
    
    public $timeout = 3600;
    
    public static function getInstance()
    {
        if (self::$_instance == null) 
        {
            self::$_instance = new Output();
        }
        
        return self::$_instance;
    }

    public function __toString()
    {
        return $this->output;
    }
    
    public function startCapture()
    {
        ob_start();
    }
    
    public function endCapture($filepath, $argv)
    {
        $this->buffer[$this->makeId($filepath, $argv)] = ob_get_contents();
        ob_clean();
    }
    
    public function makeId($filepath, $argv)
    {
        $argv = serialize($argv);
        $name = $filepath.'__'.md5($filepath.'__'.$argv);
        
        return $name;
    }
    
    public function __sleep(){}
    public function __wakeup(){}
    public function fetchAll()
    {
        return $this->buffer;
    }

}

?>
