<?php 
namespace Phoenix\Html;

class Link {
    
    private $_url = '', $_attr = '', $_text = 'Link';
    protected $_module = null,
                $_controller = null,
                $_action = null,
                $_language = null,
                $_params = null;
    
    public function __construct($text, $pattern, $link = array(), $attr = array()) {
        if (array_key_exists('module', $link)) {
            $this->_module = $link['module'];
            unset($link['module']);
        }
        
        if (array_key_exists('controller', $link)) {
            $this->_controller = $link['controller'];
            unset($link['controller']);
        }
        
        if (array_key_exists('action', $link)) {
            $this->_action = $link['action'];
            unset($link['action']);
        }
        
        if (array_key_exists('lang', $link)) {
            $this->_language = $link['lang'];
            unset($link['lang']);
        }
        
        if (array_key_exists('params', $link)) {
            $this->_params = implode('/', $link['params']);
            unset($link['params']);
        }
        
        $this->_url = $url;
        $this->_attr = $attr;
        $this->_text = $text;
    }
    
    
    public function __toString() {
        $module = str_replace(':module', $this->_module, $this->_url);
        $controller = str_replace(':controller', $this->_controller, $module);
        $action = str_replace(':action', $this->_action, $controller);
        $lang = str_replace(':lang', $this->_language, $action);
        $param = str_replace(':params', $this->_params, $lang);
        
        $link = '<a href="' . $param . '" ';
        
        foreach ($this->_attr as $key => $value) {
            $link .= $key . '="'.$value.'" ';
        }
        
        $link .= '>'.$this->_text.'</a>';
        
        return $link;
    }
}

?>