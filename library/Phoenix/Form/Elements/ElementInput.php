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
namespace Phoenix\Form\Elements;

class ElementInput {

    protected $props;
    protected $_name, $_type;
    protected $label = array();
    
    
    public function __construct($name, $data) {
        if (!array_key_exists('type', $data))
                throw new \InvalidArgumentException('There should always be a "type" key in every element definition');
        
        $this->_name = $name;
        $this->_type = $data['type'];
        
        unset($data['type']);
        
        $this->props = $data;
        
        $this->setLabel()
                ->setFilters();
    }
    
    private function setLabel()
    {
        
        if (array_key_exists('label', $this->props)):
            $this->label['text'] = $this->props['label']['text'];
            $this->label['for'] = $this->props['label']['for'];
            unset($this->props['label']);
        endif;
        
        return $this;
    }
    
    public function __get($name)
    {
        switch($name):
        case 'name':
            return $this->_name;
            break;
        default:
            if(array_key_exists($name, $this->props))
                return $this->props;
            else
                return null;
        endswitch;
    }
    
    
    public function __toString()
    {   
        $element = '';
        if (!empty($this->label)) {
            $element .= '<label for="'.$this->label['for'].'">';
            $element .= $this->label['text'];
            $element .= '</label>';
        }
        $element .= '<input name="'.$this->_name.'" type="'.$this->_type.'" ';
        foreach ($this->props as $key => $value):
            $element .= "$key=\"$value\" ";
        endforeach;
        $element .= ' /><br />';
        
        return $element;
    }

}

?>
