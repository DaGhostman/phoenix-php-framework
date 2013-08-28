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
namespace Phoenix\Form;
use Phoenix\Form\Elements\ElementInput;

class FormFactory {

    protected $_name = 'form_', $_action, $_method = 'POST', $_target = '_SELF';
    public function __construct() {
        
    }

    public function buildForm(array $props)
    {
        if (array_key_exists('name', $props)){ 
            $this->_name = $props['name']; 
            unset($props['name']);
        }
        if (array_key_exists('action', $props)) {
                $this->_action = $props['action']; 
                unset($props['action']);
        }
        if (array_key_exists('method', $props)) {
            $this->_method = $props['method']; 
            unset($props['method']);
        }
        //if (array_key_exists)
        foreach($props as $key => $value):
            $this->elements .= new ElementInput($key, $value);
        endforeach;
    }
    
    public function __toString() {
        $form = '<form name="' . $this->_name . '" method="' . $this->_method . '"';
        $form .= ($this->_action ? ' action="' . $this->_action . '"' : 
            ' target="' . $this->_target . '"') . '>';
        $form .= $this->elements;
        $form .= '</form>';
        
        return $form;
    }

}

?>
