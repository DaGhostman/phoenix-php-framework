<?php 
namespace Phoenix\Html\Table;

class Cell {
    private $_data = '', $_attr = '';
    
    public function __construct($data, $attr) {
        $this->_data = $data;
        
        foreach ($attr as $key => $value) {
            $this->_attr .= ' '.$key.'="'.$value.'"';
        }
    }
    
    
    public function __toString() {
        return '<td '.$this->_attr.'>'.$this->_data.'</td>';
    }
}


?>