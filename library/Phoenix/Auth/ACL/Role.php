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
class Role {

    protected $_name = '',
            $_privilege = null,
            $_inherited = null,
            $_allowInheritance = true;
    
    /**
     * Constructor for every new role.
     * Used to match the Role::$_name 
     * against the user role entry and to 
     * contain the Privilege coresponding to the role.
     * 
     * @access public
     * @param string $name
     * @param Privilege $priv
     */
    public function __construct($name, Privilege $priv)
    {
        $this->_name = $name;
        $this->_privilege = $priv;
    }
    
    /**
     * Getter for the role name
     * 
     * @access public
     * @return string
     */
    public function getName() {
        return $this->_name;
    }
    
    /**
     * Getter for the Privilege Object
     * 
     * @access public
     * @return Privilege
     */
    public function getPrivilege() {
        return $this->_privilege;
    }
    
    
    /**
     * Setter for the inherited role
     * 
     * @return array Returns an array with the 
     */
    public function setParent(Role $role) {
        
        if ($this->_allowInheritance == true):
            $this->_inherited = $role;
        endif;
        return $this;
    }
    
    /**
     * Checks if the role has a parent
     * 
     * @access public
     * @return bool
     */
    public function hasParent() {
        return ($this->_inherited ? true : false);
    }
    
    /**
     * Getter for the parent role
     * 
     * @access public
     * @return Role The inherited role object.
     */
    public function getParentRole()
    {
        return $this->_inherited;
    }
    
    /**
     * Either allows the role to be extended or not.
     * 
     * @param bool $state True to allow the role to be extended
     */
    public function allowInheritance($state = true)
    {
        $this->_allowInheritance = $state;
        
        return $this;
    }
}

?>