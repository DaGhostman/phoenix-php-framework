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

namespace Phoenix\View;

use Phoenix\Application\ErrorManager;
use Phoenix\Router\Request;
use Phoenix\View\Translate;
class Viewer  {
    
    private static $_instance = null;
    
    protected $viewContents;
    protected $tpl = array();
    protected $language = null;
    protected $_translate = null, $_config = null;
    protected $_template = null;
    protected $_compress = true;
    protected $output = true;
    
    public function setTranslator($translator) {
        $this->_translate = $translator;
    }
    
    public function __construct($route, $conf)
    {
        $this->_config = &$conf;
        
        if ($this->output != false) {
            $module = $route['module'];
            $controller = $route['controller'];
            $action = $route['action'];
            
            $this->language = $route['language'];
            
            $this->_translate = new Translate($this->_config, $this->language);
            
            $this->_template = APPLICATION_PATH . 
                $conf['core-application.module.path'] . 
                DIRECTORY_SEPARATOR . $module . 
                $conf['core-application.view.path'] .
                DIRECTORY_SEPARATOR . $controller .
                DIRECTORY_SEPARATOR . $action.'.phtml';
                
        }
        
    }

    private function getContents() {
        if (is_readable($this->_template)):
            ob_start();
            include_once($this->_template);
            $this->viewContents = ob_get_contents();
            ob_end_clean();
        else:
            throw new \OutOfRangeException('
               Unable to find the template: ' . 
               $this->_template, 503
            );
        endif;
    }
    
    protected function compressed() {
        $this->getContents();
        
        $search = array(
        	'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
        	'/[^\S ]+\</s',  // strip whitespaces before tags, except space
        	'/(\s)+/s'       // shorten multiple whitespace sequences
        );
            
        $replace = array(
            '>',
            '<',
            '\\1'
        );
            
        $trimed = preg_replace($search, $replace, $this->viewContents);
        $minified = preg_replace('/<!--(.*)-->/Uis', '', $trimed);
        
        return $minified;
    }
    
    protected function generic() {
        $this->getContents();
        
        return $this->viewContents;
    }
	
	public function __set($key, $value)
	{
		$this->tpl[$key] = $value;
	}
	
	public function __get($key)
	{
        if (array_key_exists($key, $this->tpl))
		    return $this->tpl[$key];
        else
          return null;
	}
        
    public function sendOutput($state)
    {
        $this->output = $state;
    }
    
    public function translate($string) {
        
        if (empty($this->_translate)) {
            $this->_translate = new Translate($this->_config, $this->language);
        }
        
        return $this->_translate->translate($string);
    }
    
    public function getCurrentLanguage() {
        return $this->_translate->getCurrentLanguage();
    }
    
    public function __destruct() {
        if ($this->output == false) return;
        switch ($this->_compress) {
            case true:
                print $this->compressed();
                break;
            default:
                print $this->generic();
                break;
        } 
    }
	
}