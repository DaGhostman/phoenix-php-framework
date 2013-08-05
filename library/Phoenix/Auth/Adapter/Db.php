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
use Phoenix\Auth2\Adapter\IAdapter;
use Phoenix\Db\Orm\AccessLayer;
use Phoenix\Core\HttpErrorsManager;
use Phoenix\Router\Response;
use Phoenix\Core\SignalSlot\Manager;
use Phoenix\Core\SignalSlot\Signals;
use Phoenix\Storage\Registry;
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
 * @uses Phoenix\Core\SignalSlot\Manager
 * @uses Phoenix\Core\SignalSlot\Signals
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
     * @emits \Phoenix\Core\SignalSlot\Signals::SIGNAL_AUTH_SUCCESS
     * @emits \Phoenix\Core\SignalSlot\Signals::SIGNAL_AUTH_FAIL
     * @return \Phoenix\Auth2\Adapter\Db
     */
    public function authenticate() {
        $this->__identity = $this->__access->findAll($this->__fields);
        $this->__identity = $this->__identity[0];
        
        if (!empty($this->__identity)):
            Manager::getInstance()
            ->emit(Signals::SIGNAL_AUTH_SUCCESS, 
                    $this->__identity);
        
        $this->__access->update(
                array($this->__tokenField => session_id()), 
                $this->__fields
                );
        else:
            Manager::getInstance()
                ->emit(Signals::SIGNAL_AUTH_FAIL,
                        $this->__fields);
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
     * @emits \Phoenix\Core\SignalSlot\Signals::SIGNAL_AUTH_SAVE_REG
     * @emits \Phoenix\Core\SignalSlot\Signals::SIGNAL_AUTH_SAVE_SESS
     * @emits \Phoenix\Core\SignalSlot\Signals::SIGNAL_AUTH_SAVE_DB
     * @emits \Phoenix\Core\SignalSlot\Signals::SIGNAL_AUTH_SAVE_ALL
     * @return \Phoenix\Auth2\Adapter\Db
     */
    public function saveIdentity($store = self::SAVE_REG) {
        
        switch ($store):
            case self::SAVE_REG:
                    foreach ($this->__identity as $key => $value):
                        Registry::set($key, $value, 'IDENTITY_STORE');
                    endforeach;
                    
                    Manager::getInstance()->emit(
                        Signals::SIGNAL_AUTH_SAVE_REG,
                        $this->__identity
                        );
                break;
            case self::SAVE_DB:
                    $this->__access->update($this->__identity, 
                            array(
                                $this->__tokenField => session_id()
                            ));
                
                    Manager::getInstance()->emit(
                        Signals::SIGNAL_AUTH_SAVE_DB,
                        $this->__identity
                        );
                break;
            case self::SAVE_SESS:
                Session::set(session_id(), $this->__identity);
                
                Manager::getInstance()->emit(
                        Signals::SIGNAL_AUTH_SAVE_SESS,
                        $this->__identity
                        );
                break;
            case self::SAVE_ALL:
                $this->saveIdentity(self::SAVE_REG);
                $this->saveIdentity(self::SAVE_DB);
                $this->saveIdentity(self::SAVE_SESS);
                
                Manager::getInstance()->emit(
                        Signals::SIGNAL_AUTH_SAVE_ALL,
                        $this->__identity
                        );
                break;
        endswitch;
        
        return $this;
    }

    public function getIdentity(array $fields = array(), $store = self::SAVE_REG) {
        
        if (empty($this->__identity)):
            switch ($store):
                case self::SAVE_DB:
                    $this->__identity = $this->__access->findAll(
                        array($this->__tokenField => session_id())
                        );
                    $this->__identity = $this->__identity[0];
                    break;
                case self::SAVE_REG:
                    $this->__identity = Registry::raw('IDENTITY_STORE');
                    break;
                case self::SAVE_SESS:
                    Session::get(session_id());
                    break;
            endswitch;
        endif;

            $id = array();
        
            if (!empty($fields)):
                foreach ($fields as $field):
                $id[$field] = $this->__identity[$field];
                endforeach;
            
                return $id;
            
            else:
                return $this->__identity;
            endif;
        
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
