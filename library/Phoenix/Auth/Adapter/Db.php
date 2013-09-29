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
use Phoenix\Db\Crud\AccessLayer;
use Phoenix\Core\HttpErrorsManager;
use Phoenix\Router\Response;
use Phoenix\Storage\Session;



/**
 * Database authentication adapter returned by 
 * the Phoenix\Auth2\Factory::__construct()
 * 
 * @package Db
 * @category Phoenix\Auth2\Adapter
 * @see Factory::__construct()
 * @uses Phoenix\Auth2\Adapter\IAdapter
 * @uses Phoenix\Db\Orm\AccessLayer
 * @uses Phoenix\Core\HttpErrorsManager
 * @uses Phoenix\Router\Response
 * @uses Phoenix\Storage\Registry
 * @uses Phoenix\Storage\Session
 */

class Db extends IAdapter {

    const SAVE_ALL = 'all',
            SAVE_DB = 'database',
            SAVE_REG = 'registry',
            SAVE_SESS = 'session';
    
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
        if (!$layer instanceof AccessLayer):
            HttpErrorsManager::getInstance()->sendError(
                Response::HTTP_500,
                    new \InvalidArgumentException(
                            'DB authentication requires instance of AccessLayer'
                            )
                    );
        /*
         * @FIXME: The connection check, maybe should not exist here. 
         * AccessLayer throws an error when the instance passed to it is 
         * not connected. (REMOVE?)
         */
        elseif ($layer->getAdapter()->isConnected() == false):
            $layer->getAdapter()->connect();
        endif;
        
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
            return $this->$field = $args;
        endif;
    }

    /**
     * Preforms the actual authentication agains the fields and values passed to
     * Db::setCredential(); and Db::setIdentity();, and updates the field name
     * passed to the Db::setTokenField(); with the identifier of the current 
     * session.
     * 
     * @see Db::setCredential();
     * @see Db::setIdentity();
     * 
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
     * @param const $store constant coresponding to the storage to save the 
     * identity to. Possible options are Session, Registry, Db or all of them.
     * Session will be presisted between requests untill the user session is 
     * active
     * 
     * Registry will be lost on the next request (this should only be used if 
     * you are using a custom storage for the registry, ie. cache and etc.)
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
        Session::set('userIdentity', $this->__identity);
        
        return $this;
    }

    public function getIdentity() {
        
        if (empty($this->__identity)):
            if (($id = Session::get('userIdentity')) != false):
                $this->__identity = $id;
            else:
                $id = $this->__access->findAll(
                        array($this->__tokenField => session_id())
                        );
                $this->__identity = isset($id[0]) ? $id[0] : $id;
            endif;
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
