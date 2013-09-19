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
use Phoenix\Application\Exception\Argument;

class Memcached implements Cacher {

    public $weight = 1;
    private $obj = null;
    private $compress = false;
    private $servers = array();
    
    protected $registry = array();
    public function __construct() {
        $this->obj = new \Memcached();
    }
    
    public function setWeight($weight = 1)
    {
        $this->weight = (int) $weight;
        
        return $this;
    }
    
    public function addServer($host, $port, $weight)
    {
        if ($this->obj->addserver($host, $port, true, $weight) == true) {
            $this->servers[] = $host.':'.$port;
        } else {
            throw new Argument(
                    "Unable to add server " . $host.':'.$port
                    );
        }
        
        return $this;
    }
    
    public function addServers(array $servers)
    {
        foreach ($servers as $server):
            $this->addServer(
                    $server['host'], 
                    $server['port'], 
                    $server['weight']
                    );
        endforeach;
        
        return $this;
    }

    public function push($key, $value, $ttl = 0)
    {
        if (!$this->exists($key)) {
                $this->obj->add($key, $value, $ttl);
                $this->registry[$key] = 1;
        } else {
            throw new Argument(
                    "The key \"$key\" already exists. 
                        Please use replace to update existing entries"
                    );
        }
        
        
        return $this;
    }
    
    
    public function update($key, $value, $ttl)
    {
        if ($this->exists($key)) {
            if ($this->compress)
                $this->obj->replace($key, $value, $ttl);
            else
                $this->obj->replace($key, $value, $ttl);
        } else {
            throw new Argument(
                    'The key "'.$key.'" does not exist. 
                        Please create it first.'
                    );
        }
    }
    
    
    public function delete($key)
    {
        if ($this->exists($key))
            $this->obj->delete($key);
        $this->registry[$key] = 0;
        
        return $this;
    }
    
    
    public function serversAvailable()
    {
        return count($this->servers);
    }
    
    private function exists($key)
    {
        return ($this->registry[$key]==1);
    }
    
    public function fetch($key)
    {
        if ($this->exists($key))
            return $this->obj->get($key);
        else
            return null;
    }
    
    public function flush()
    {
        $this->obj->flush();
        
        return $this;
    }
    
    public function increment($key, $value = 1)
    {
        if ($this->exists($key))
            $this->obj->increment($key, $value);
        
        return $this;
    }
    
        public function decrement($key, $value = 1)
    {
        if ($this->exists($key))
            $this->obj->decrement($key, $value);
        
        return $this;
    }
    
    public function touch($key, $ttl)
    {
        $this->obj->touch($key, $ttl);
        
        return $this;
    }
    

}

?>
