<?php 
namespace Phoenix\Html\Table;


class Row {
    protected $_tds = '', $_attr = '';
    public function __construct(array $td, $attr) {
        foreach ($td as $cell) {
            $this->_tds .= new Cell($cell['data'], $cell['attr']);
        }
        
        foreach ($attr as $key => $value) {
            $this->_attr .= ' '.$key.'="'.$value.'"';
        }
    }
    
    
    public function __toString() {
        return '<tr '.$this->_attr.'>'.$this->_tds.'</tr>';
    }
}


?>