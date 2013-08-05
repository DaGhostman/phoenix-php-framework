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
namespace Phoenix\Auth\Acl;
class Privilege {
    
    protected $__allow = array(),
            $__deny = array();
    
    protected $___defaultAllow = false;
    protected $___defaultDeny = true;
    
    /**
     * Sets the default policy for the allow checks.
     * If set to true if an activity is not found in the definition
     * the function will return true, flase otherwise.
     * Defaults to false.
     * 
     * @param bool $state
     * @return \Privilege
     */
    public function setDefaultAllow($state)
    {
        $this->___defaultAllow = $state;
        
        return $this;
    }
    
    
    /**
     * Sets the default policy for the allow checks.
     * If set to true if an activity is not found in the definition
     * the function will return true, flase otherwise.
     * Defaults to true.
     * 
     * @param bool $state
     * @return \Privilege
     */
    public function setDefaultDeny($state)
    {
        $this->___defaultDeny = $state;
        
        return $this;
    }
    
    public function allowEntryExists($entry)
    {
        return array_key_exists($entry, $this->__allow);
    }
    
    public function denyEntryExists($entry)
    {
        return array_key_exists($entry, $this->__deny);
    }

    /**
     * Pushes an $action entry in the allow list
     * 
     * @param string $action the action to add in the allow list
     * @return \Privilege
     */
    public function allow($action) {
        array_push($this->__allow, $action);
        
        return $this;
    }
    
    /**
     * Pushes an $rule entry in the deny list
     * 
     * @param string $action the action to add in the deny list
     * @return \Privilege
     */
    public function deny($action) {
        array_push($this->__deny, $action);
        
        return $this;
    }
    
    /**
     * Performs a check to see if the $action is allowed.
     * 
     * @param type $action action to check for
     * @return type
     */
    public function isAllowed($action) {
        if(in_array($action, $this->__allow))
        {
            return $this->__allow[$action];
        } else {
            return $this->___default;
        }
    }
    
    /**
     * Performs a check to see if the $action is denied.
     * 
     * @param type $action action to check for
     * @return type
     */
    public function isDenied($action) {
        if(in_array($action, $this->__deny))
        {
            return $this->__allow[$action];
        } else {
            return $this->___default;
        }
    }
    
    
    

}

?>
