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

namespace Phoenix\Cache\Front;
use Phoenix\Cache\Cacheable;
use Phoenix\Application\Exception\Runtime;
use Phoenix\Cache\Broker;


class Execution implements Cacheable {
    protected $storage = array();
    public $timeout = 360;
    
    
    public function setTimeout($sec)
    {
        $this->timeout = $sec;
        
        return $this;
    }
    
    
    public function call($callback, $argv){
        if (is_callable($callback, true, $name)) {
            $key = $this->makeId($callback, $argv);

            if (in_array($key, $this->storage)) {
                $result = $this->storage[$key];
            } elseif (($result = Broker::getInstance()->fetch($key))) {
                $this->storage[$key] = $result;
            } else {
                $this->storage[$key] = call_user_func_array($callback, $argv);
                $result = $this->storage[$key];
            }
        } else {
            throw new Runtime("The supplied callback is not callable");
        }
        
        return $result;
    }
    
    public function makeId($callback, $argv)
    {
        if (is_callable($callback, true, $name)) {
            return $name . '__' . hash('crc32', serialize($argv));
        } else {
            throw new Runtime('The supplied callback is not callable');
        }
    }
    
    public function __sleep(){}
    public function __wakeup(){}
    public function fetchAll(){
    	return $this->storage;
    }
    
}
?>
