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

class File implements Cacher {

    /**
     * Adds a directory to be used for caching files
     * 
     * @param string $host The directory to use for cachefiles
     * @param type $port ommited in File Cacher
     * @param type $ttl time in seconds defined as lifetime
     */
    
    private $__registry = null, $__ttl = 360;
    public $weight = 1;
    public function addServer($host, $port = null, $ttl = null) {
        $this->__registry = $host;
        $this->__ttl = $ttl ? $ttl : $this->__ttl;
        
        return $this;
    }
    
    public function setWeight($weight = 1)
    {
        $this->weight = (int) $weight;
    
        return $this;
    }

    public function addServers(array $servers) {}

    public function decrement($key, $value = 2) {}

    public function exists($key) {
        if (file_exists($this->__registry.DIRECTORY_SEPARATOR.$key)) {
            $stat = stat($this->__registry.DIRECTORY_SEPARATOR.$key);
            
            if ((time() - $stat['mtime']) < $this->__ttl) {
                return true;
            } else {
                unlink($this->__registry.DIRECTORY_SEPARATOR.$key);
                return false;
            }
        } else {
            return false;
        }
    }

    public function fetch($key) {
        if ($this->exists($key)) {
            return file_get_contents($this->__registry.DIRECTORY_SEPARATOR.$key);
        }
    }

    public function increment($key, $value = 1) {}

    public function push($key, $value, $ttl) {
        if ($this->exists($key)) {
            throw new Argument("The file you are trying to set alredy exists, please use update");
        } else {
            $fp = fopen($this->__registry.DIRECTORY_SEPARATOR.$key, 'w+');
            flock($fp, LOCK_EX | LOCK_NB);
            fwrite($fp, $value);
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }

    public function serversAvailable() {
        return count($this->__registry);
    }

    public function touch($key, $ttl) {
        touch($this->__registry.$key);
        
        return $this;
    }

    public function update($key, $value, $ttl) {
        if ($this->exists($key)) {
            $fp = fopen($this->__registry.DIRECTORY_SEPARATOR.$key, 'w+');
            flock($fp, LOCK_EX | LOCK_NB);
            fwrite($fp, $value);
            flock($fp, LOCK_UN);
            fclose($fp);
        } else {
            throw new Argument("The file you are trying to update does not exist, please use push");
        }
    }

}

?>
