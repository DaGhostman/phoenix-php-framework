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

namespace Phoenix\Auth\ACL;
class AccessList {

    private static $_instance = null;
    protected $_list = null;
    private function __construct() {
        $this->_list = array();
    }
    
    /**
     * @access public
     * @static
     * Prevents instantiation of multiple lists
     * 
     * @return AccessList instance of the object
     */
    public static function getInstance() {
        if (self::$_instance === null & !self::$_instance instanceof AccessList):
            self::$_instance = new AccessList();
        endif;
        
        return self::$_instance;
    }
    
    /**
     * Adding roles to the Access List
     * 
     * @param Role $role Ready build role to push to the list
     * @return \AccessList 
     */
    public function pushEntry(Role $role) {
        $this->_list[$role->getName()] = $role;
        
        return $this;
    }
    
    /**
     * Cechking if a $role is allowed to perform $action
     * 
     * @param string $role_name the role against which the check should be made.
     * @param string $action the action to check the role for
     * @return bool True if the action is allowed
     */
    public function isAllowed($role_name, $action) 
    {
        if (array_key_exists($role_name, $this->_list)):
            $role = $this->_list[$role_name];
            if ($role->hasParent()):
                while($role->hasParent()):
                $priv = $role->getParent()->getPrivilege();
                if ($priv->allowEntryExists() || $priv->denyEntryExists()):
                    return $priv->isAllowed($action);
                else:
                    $role = $role->getParent();
                endif;
                endwhile;
                
            else:
                return $role->getPrivilege()->isAllowed($action);
            endif;
        endif;
    }
    
    
    /**
     * Cechking if a $role is not allowed to perform $action
     * 
     * @param string $role_name the role against which the check should be made.
     * @param string $action the action to check the role for
     * @return bool True if the action is not allowed
     */
    public function isDenied($role_name, $action)
    {
        if (array_key_exists($role_name, $this->_list)):
            $role = $this->_list[$role_name];
            if ($role->hasParent()):
                while($role->hasParent()):
                $priv = $role->getParent()->getPrivilege();
                if ($priv->denyEntryExists($action)):
                    return $priv->isDenied($action);
                else:
                    $role = $role->getParent();
                endif;
                endwhile;
                
            else:
                return $role->getPrivilege()->isDenied($action);
            endif;
        endif;
    }
    

}

?>