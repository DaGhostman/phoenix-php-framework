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
namespace Phoenix\Auth;

use Phoenix\Auth\Adapter\IAdapter;
use Phoenix\Router\Response;
use Phoenix\Core\HttpErrorsManager;

use Phoenix\File\Cvs;
use Phoenix\File\Json;
use Phoenix\File\Ini;

class Http extends IAdapter {
    
    private $_fp;
    protected $authenticated = null;


    public function __construct($fp)
    {
        $this->_fp = $fp;
        
        if (!isset($_SERVER['PHP_AUTH_USER'])):
        Response::getInstance(Response::V11)
                ->addHeaders(
                        array(
                            Response::HTTP_401,
                            'WWW-Authenticate: Digest realm=Login Required",
                                qop="auth",nonce="'. uniqid(md5(microtime()), true).'",
                                    opaque="'.md5('Login Required').'"'
                            )
                        )
                ->send();
        
        HttpErrorsManager::getInstance()
                ->sendError(
                        Response::HTTP_403, 
                        new \InvalidArgumentException(
                                'Unauthenticated user, tryed to access the restricted area.'
                                )
                        );

                        exit();
        endif;
    }
    
    
    public function authenticate()
    {
        $user = $_SERVER['PHP_AUTH_USER'];
        
        if ($this->_fp instanceof Ini) {
            if (!isset($this->_fp->$user) | $this->_fp->$user !== $_SERVER['PHP_AUTH_PASS']) {
                $this->authenticated = FALSE;
            } else {
                $this->authenticated = TRUE;
            }
        } elseif ($this->_fp instanceof Json) {
            if (!isset($this->_fp->$user) | $this->_fp->$user !== $_SERVER['PHP_AUTH_PASS']) {
                $this->authenticated = FALSE;
            } else {
                $this->authenticated = TRUE;
            }
        } elseif ($this->_fp instanceof Cvs) {
            if (!isset($this->_fp->$user) | $this->_fp->$user !== $_SERVER['PHP_AUTH_PASS']) {
                $this->authenticated = FALSE;
            } else {
                $this->authenticated = TRUE;
            }
        } else {
            HttpErrorsManager::getInstance()->sendError(
                Response::HTTP_500,
                new \InvalidArgumentException(
                    'Unkonwn source for authentication'
                )
            );
        }
    }
    
    public function hasIdentity() {
        return $this->authenticated;
    }

    public function __call($name, $params) {
        
    }

    public function __get($name) {
        
    }

    public function __set($name, $value) {
        
    }

    public function getIdentity(array $fields = array()) {
        
    }

    public function setCredential($value, $field) {
        
    }

    public function setIdentity($value, $field) {
        
    }

    public function setTokenField($field) {
        
    }
    
}


?>