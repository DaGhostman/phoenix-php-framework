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
namespace Phoenix\Auth\Adapter;

abstract class IAdapter {

    public abstract function __construct();
    public abstract function __get($name);
    public abstract function __set($name, $value);
    public abstract function __call($name, $params);
    
    abstract public function authenticate();
    abstract public function getIdentity();
    abstract public function hasIdentity();
    
    abstract public function setCredential($value, $field);
    abstract public function setIdentity($value, $field);
    abstract public function setTokenField($field);

}

?>
