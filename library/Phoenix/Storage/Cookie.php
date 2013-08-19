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

namespace Phoenix\Storage;
use Phoenix\Application\Configurator;
class Cookie {

    static $_instance = null;
    protected $_ttl, $_domain, $_path, 
            $_secure, $_http, $_prefix, $_suffix;
    public static function getInstance()
    {
        if (self::$_instance === null):
            self::$_instance = new Cookie();
        endif;
        
        return self::$_instance;
    }
    
    public function construct()
    {
        $cfg = new Configurator('application/config/application.ini', true);
        $this->_ttl = $cfg->cookie->ttl ? $cfg->cookie->ttl : 3600;
        $this->_domain = $cfg->cookie->domain ? $cfg->domain : $_SERVER['SERVER_NAME'];
        $this->_path = $cfg->cookie->path ? $cfg->cookie->path : '/';
        $this->_secure = $cfg->cookie->secure ? $cfg->cookie->secure : false;
        $this->_http = $cfg->cookie->http ? $cfg->cookie->http : true;
        $this->_prefix = $cfg->cookie->prefix ? $cfg->cookie->prefix : '';
        $this->_suffix = $cfg->cookie->suffix ? $cfg->cookie->suffix : '';
        $this->_encode = $cfg->cookie->encode ? $cfg->cookie->encode : false;
    }
    
    public function create($name, $value, $ttl = false, $domain = false, $path = false, $secure = false, $http=true)
    {
        $_ttl = $ttl ? $ttl : $this->_ttl;
        $_domain = $domain ? $domain : $this->_domain;
        $_path = $path ? $path : $this->_path;
        $_secure = $secure ? $secure : $this->_secure;
        $_http = $http ? $http : $this->_http;
        
        switch ($this->_encode):
            case 'base64':
                $_val = base64_encode($value);
                break;
            default:
                $_val = $value;
                break;
        endswitch;
        
        setcookie($this->_prefix.$name.$this->_suffix, $_val, $_ttl, $_path, $_domain, $_secure, $_http);
    }
    
    public function get($name)
    {
        switch ($this->_encode):
            case 'base64':
                return base64_decode($_COOKIE[$this->_prefix.$name.$this->_suffix]);
                break;
            default:
                return $_COOKIE[$this->_prefix.$name.$this->_suffix];
                break;
        endswitch;
        
    }

}

?>
