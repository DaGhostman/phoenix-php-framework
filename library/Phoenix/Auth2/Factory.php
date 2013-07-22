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

namespace Phoenix\Auth2;
use Phoenix\Auth2\Adapter\Db;
use Phoenix\Auth2\Adapter\Http;
use Phoenix\Core\HttpErrorsManager;
use Phoenix\Router\Response;

class Factory {

    const AUTH_DB = 1,
            AUTH_HTTP = 2;
    
    protected $__instance = null;
    
    public function __construct($adapter, $arg) {
        switch ($adapter):
            case self::AUTH_DB:
                $this->__instance = new Db($arg);
                break;
            case self::AUTH_HTTP:
                $this->__instance = new Http($arg);
                break;
            default:
                HttpErrorsManager::getInstance()->send(
                Response::HTTP_503,
                        new \InvalidArgumentException(
                                'Invalid Adapter supplied to Auth\\Factory'
                                )
                        );
        endswitch;
        
        return $this->__instance;
    }

}

?>
