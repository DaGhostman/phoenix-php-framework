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

class KeyStore implements Cacheable
{
    private $timeout = 360;
    protected $storage = array();
    
    public function setTimeout($timeout)
    {
        $this->timeout = (int) $timeout;
        
        return $this;
    }
    
    public function attach($key, $value)
    {
        if (!array_key_exists($key, $this->storage))
        {
            $this->storage[$key] = $value;
            
            return true;
        } else {
            throw new Runtime("The supplied key {$key} already exists. To override please use replace() method");
            return false;
        }
    }
    
    public function replace($key, $value)
    {
        if (array_key_exists($key, $this->storage))
        {
            $this->storage[$key] = $value;
            return true;
        } else {
            throw new Runtime("The specified key {$key} does not exist. Please set it first, before replacing.");
            return false;
        }
    }
    
    public function get($key)
    {
        if (array_key_exists($key, $this->storage))
        {
            return $this->storage[$key];
        } elseif (($result = Broker::getInstance()->fetch($key))) {
            return $result;
        } else {
            return null;
        }
    }
    

    public function fetchAll()
    {
    	return $this->storage;
    }

    public function __wakeup(){}
    public function __sleep(){}
}

?>