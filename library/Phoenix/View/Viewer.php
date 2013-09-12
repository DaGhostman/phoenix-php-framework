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

use Phoenix\Core\HttpErrorsManager;
use Phoenix\View\Translate;
class Viewer implements \ArrayAccess{
    
    private static $_instance = null;
    
    private $viewContents;
    private $view = null;
    private $tpl = null;
    protected $compiler = null;
    
    private static $uri;
    public $_helper = null;
    protected $_translate = null;
    
    protected $output = true;
        
    public $compile = FALSE;
    
    protected function __construct($uri)
    {
        //$this->_helper = new Helper;
        
        $this->uri = $uri;
                
        return $this;
    }
    
    public function setTemplate($templateName)
    {
        $this->view = $templateName;
        
        return $this;
    }
    
    private function prepare()
    {
        
        $module = $this->uri['module'];
        $controller = $this->uri['controller'];
        $action = ($this->view ? $this->view : $this->uri['action']);
        
        $conf = \Phoenix\Storage\Registry::get('config', 'SystemCFG');
        
        try {
            if (is_readable(APPLICATION_PATH . '/modules/'.$module.'/views/'.$controller.'/'.$action.'.phtml')):

                ob_start();
                include_once(APPLICATION_PATH . $conf['application.module.path'] .
                     DIRECTORY_SEPARATOR . $module . $conf['application.view.path'] .
                     DIRECTORY_SEPARATOR . $controller . DIRECTORY_SEPARATOR
                     . $action . '.phtml'
                    );
                $this->viewContents = $this->compress(ob_get_contents());
                ob_end_clean();
            else:
                throw new \OutOfRangeException(
                    'Unable to find the template for : ' . 
                    $_SERVER['HTTP_HOST'] . '/' . $module . '/' . 
                    $controller . '/' . $action
                    );
            endif;
            
        } catch (\OutOfRangeException $e) {
            HttpErrorsManager::getInstance()->sendError(404, $e);
            
        }
    }
        
        public function compress($content) {
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
            
            $trimed = preg_replace($search, $replace, &$content);
            $minified = preg_replace('/<!--(.*)-->/Uis', '', &$trimed, -1);
            return $minified;
    }
	
	
	public static function getInstance($uri = '')
	{	
		if (self::$_instance == false)
			self::$_instance = new Viewer($uri);
		elseif (!self::$_instance instanceof Viewer)
			self::$_instance = new Viewer($uri);
		
		return self::$_instance;
	}
	
	public function __set($key, $value)
	{
		$this->tpl[$key] = $value;
	}
	
	public function setCache($state)
	{
		$this->compile = $state;
	}
	
	public function __get($key)
	{
            if (array_key_exists($key, $this->tpl))
		return $this->tpl[$key];
            else
                return null;
	}
        
        public function sendOutput($state = false)
        {
            $this->output = $state;
        }
	
	public function render()
	{
            $this->prepare();
            if ($this->output == false) $this->viewContents = '';
            print $this->viewContents;
	}
        
        public static function resetInstance()
        {
            self::$_instance = null;
        }

    public function offsetExists($offset) {
        return array_key_exists($offset, $this->tpl);
    }
    
    public function offsetGet($offset) {
        return $this->$offset;
    }

    public function offsetSet($offset, $value) {
        $this->$offset = $value;
    }

    public function offsetUnset($offset) {
        unset($this->tpl[$offset]);
    }
    #Language warpers
    
    public function translate($string) {
        
        if (empty($this->_translate)) {
            $this->_translate = Translate::getInstance();
        }
        
        return $this->_translate->translate($string);
    }
    
    public function getCurrentLanguage() {
        return $this->_translate->getCurrentLanguage();
    }
    
    
	
}