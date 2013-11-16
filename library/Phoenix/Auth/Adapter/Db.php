<?php

/**
 * 
 * @author Dimitar Dimitrov <daghostman.dd@gmail.com>
 * @link http://web-forge.org/
 * @copyright (c) 2013, Dimitar Dimitrov
 * @license  GNU GPLv3
 * Phoenix PHP Framework - Another MVC PHP framework
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
use Phoenix\Auth\Adapter\IAdapter;



/**
 * Database authentication adapter returned by 
 * the Phoenix\Auth2\Factory::__construct()
 * 
 * @package Auth\Adapter
 * @category Phoenix\Auth\Adapter
 * @see Factory::__construct()
 * @uses Phoenix\Auth2\Adapter\IAdapter
 */

class Db extends IAdapter {
    
    protected $__identity = null,
                $__access = null,
                $__fields = array(),
                $__tokenField = null;
    
    /**
     * Preforms a check for valid ORM object and for an active connection
     * to the database. If the connection is not established, trys to connect.
     * @access public
     * @param \Phoenix\Db\Orm\AccessLayer $layer A configured object to use for the authentication
     */
    public function __construct($layer) {
        try {
            if (!is_object($layer)) {
                throw new \RuntimeException('Argument should be object');
            }
        } catch (\RuntimeException $e) {
            throw new \RuntimeException('Error while setting up Auth module',
                null,
                $e);
        }
        
        $this->__access = $layer;
    }

    public function __get($name) {
        return $this->__identity[$name];
    }

    public function __set($name, $value) {
        $this->__identity[$name] = $value;
        
        return $this;
    }

    public function __call($name, $args) {
        if (strpos($name, 'get') === 0):
            $field = substr($name, 3, strlen($name));
            return $this->$field;
        elseif (strpos($name, 'set') === 0):
            $field = substr($name, 3, strlen($name));
            
            $this->$field = $args;
        endif;
    }

    /**
     * Preforms the actual authentication agains the fields and values passed to
     * Db::setCredential(); and Db::setIdentity();, and updates the field name
     * passed to the Db::setTokenField(); with the identifier of the current 
     * session.
     * @see Db::setCredential();
     * @see Db::setIdentity();
     * @access public
     * @return \Phoenix\Auth2\Adapter\Db
     */
    public function authenticate() {
        
        $this->__identity = $this->__access->findAll($this->__fields);
        $this->__identity = isset($this->__identity[0]) ? $this->__identity[0] : $this->__identity;
        
        if (!empty($this->__identity)):
        
        $this->__access->update(
                array($this->__tokenField => session_id()), 
                $this->__fields
                );
        endif;
        
        return $this;
    }
    
    /**
     * 
     * Db will update the user profile with the changes which will be permanent
     * 
     * @return \Phoenix\Auth2\Adapter\Db
     */
    public function saveIdentity() {
        
        $this->__access->update(
            array($this->__tokenField => session_id()),
            $this->__fields
        );
        
        
        return $this;
    }

    public function getIdentity() {
        
        if (empty($this->__identity)):
            	$this->__access->setIdColumn($this->__tokenField);
                $id = $this->__access->findById(session_id());
                
                $this->__identity = isset($id[0]) ? $id[0] : $id;
        endif;

        return $this->__identity;
        
    }

    /**
     * Checks if the identity store is empty
     * 
     * @access public
     * @return bool
     */
    public function hasIdentity() {
        $this->getIdentity();
        
        return !empty($this->__identity);
    }

    /**
     * Sets the credential value and field to be used.
     * Note: the user credentials($value) should be manipulated
     * before passed to the function.
     * 
     * @access public
     * @param string $value the user inputed value to use as credential
     * @param string $field the field in the database
     * @return \Phoenix\Auth2\Adapter\Db
     */
    public function setCredential($value, $field) {
        $this->__fields[$field] = $value;
        
        return $this;
    }

    /**
     * Sets the identity value and field to be used
     * 
     * @access public
     * @param string $value the user input for the identity
     * @param string $field the field in the database 
     * @return \Phoenix\Auth2\Adapter\Db
     */
    
    public function setIdentity($value, $field) {
        $this->__fields[$field] = $value;
        
        return $this;
    }

    /**
     * Used to set the token field. usually the field in the database table 
     * where the session_id is stored. Should be set before triggering Db::authenticate()
     * 
     * @see Db::authenticate()
     * 
     * @param string $field
     * @return \Phoenix\Auth2\Adapter\Db
     */
    public function setTokenField($field) {
        
        $this->__tokenField = $field;
        
        return $this;
    }
    

}
?>
