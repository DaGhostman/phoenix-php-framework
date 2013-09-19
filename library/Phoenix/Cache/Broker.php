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
use Phoenix\Cache\Cacher;
class Broker {

    protected static $_instance = null;
    protected $active_cachers = 2;
    private $serversPool = array(),
            $hooksPool = array();
    
    private function __construct() {}
    private function __clone() {}
    
    public static function getInstance()
    {
        if (self::$_instance == null) 
            self::$_instance = new Broker();
        
        return self::$_instance;
    }
    
    public function newInstance()
    {
        return new Broker();
    }

    public function addBackend($cacher)
    {
        if ($cacher instanceof Cacher)
            $this->serversPool[] = $cacher;
        
        usort($this->serversPool, function($a, $b) {
            if ($a->weight == 0) return 0;
            else ($a->weight < $b->weight) ? -1 : 1;
        });        
        
        return $this;
    }
    
    public function setActiveCachers($num)
    {
        $this->active_cachers = (int) $num; 
        
        return $this;
    }
    
    public function setFrontend(&$cachable, $id){
        if ($cachable instanceof Cacheable)
            $this->hooksPool[$id] = $cachable;
        
        return $this;
    }
    
    public function &getFrontend($id=null)
    {
        if (null == $id){
            return $this->hooksPool;
        } else {
            if (array_key_exists($id, $this->hooksPool))
                return $this->hooksPool[$id];
            else 
                return false;
        }
    }
    
    public function fetch($key)
    {
        foreach ($this->serversPool as $server)
        {
            if (($res = $server->fetch($key))) return $res;
        }
    }
    
    public function __destruct()
    {
        $num = 0;
        
        foreach ($this->serversPool as $server)
        {
            if ($num < $this->active_cachers):
            
                foreach($this->hooksPool as $hook)
                {
                    foreach ($hook->fetchAll() as $key => $val)
                    {
                        $server->push($key, serialize($val), $hook->timeout);
                    }
                }
                $num++;
            else:
                break ;
            endif;
        }
    }

}

?>
