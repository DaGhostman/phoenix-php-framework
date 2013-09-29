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
namespace Phoenix\Cache\Backend;
use Phoenix\Cache\Cacher;
use Phoenix\Db\Crud\AccessLayer;
use Phoenix\Application\Exception\Argument;

class Database implements Cacher {

    private $__registry = array();
    /**
     * 
     * @param \Phoenix\Db\Crud\AccessLayer $host
     * @param string $port Omited
     * @param string $ttl Omited
     * @throws Argument
     */
    public function addServer($host, $port, $ttl) {
        if (!$host instanceof AccessLayer) {
            throw new Argument("The supplied host should be a valid configured instance of Phoenix\Db\Crud\AccessLayer");
        } else {
            $this->__registry[] = $host;
        }
        
        return $this;
    }

    public function addServers(array $servers) {
        foreach ($servers as $server) {
            $this->addServer($server, NULL, NULL);
        }
        
        return $this;
    }

    public function decrement($key, $value = 2, $conditions = array()) {
        foreach($this->__registry as $layer) {
            $layer->update(array($key => $key.'-'.$value), $conditions);
        }
        
        return $this;
    }

    public function exists($key, $conditions) {
        foreach($this->__registry as $layer) {
            $result = $layer->getAdapter()
                    ->select($layer->entityTable, $conditions)
                    ->fetch(\PDO::FETCH_ASSOC);
            
            if (array_key_exists($key, $result)) {
                return true;
            }
        }
        
        return false;
    }

    public function fetch($key, $conditions) {
        foreach($this->__registry as $layer) {
            $result = $layer->getAdapter()
                    ->select($layer->entityTable, $conditions)
                    ->fetch(\PDO::FETCH_ASSOC);
            
            if (array_key_exists($key, $result)) {
                return $result[$key];
            }
        }
        
        return false;
    }

    public function increment($key, $value=1, $conditions) {
         foreach($this->__registry as $layer) {
            $layer->update(array($key => $key.'+'.$value), $conditions);
        }
        
        return $this;
    }

    public function push($key, $value, $ttl) {
        foreach($this->__registry as $layer) {
            $layer->insert(array($key => $value));
        }
        
        return $this;
    }

    public function serversAvailable() {
        return count($this->__registry);
    }

    public function touch($key, $ttl) {}

    public function update($key, $value, $ttl, $conditions) {
        foreach($this->__registry as $layer) {
            if ($this->exists($key, $conditions)) {
                $layer->update(array($key => $value), $conditions);
            }
        }
        
        return $this;
    }
}

?>
