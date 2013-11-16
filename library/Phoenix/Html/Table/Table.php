<?php 
namespace Phoenix\Html\Table;
use Phoenix\Html\Table\Row;

class Table {
    
    protected $_attr = '', 
                $_tr = '',
                $_caption = null;
    
    public function __construct($elements, $attr, $caption = null) {
        foreach ($elements as $element) {
            $this->_tr .= $element;
        }
        
        foreach ($attr as $key => $value) {
           $this->_attr .= ' '.$key.'="'.$value.'"';
        }
    }
    
    public function __toString() {
        return '<table '.$this->_attr.'>'.$this->_tr.'</table>';
    }
}


?>