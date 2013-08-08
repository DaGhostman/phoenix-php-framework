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

namespace Ext\Plugins\Install;

use Phoenix\Plugin\IPlugin;
use Phoenix\Router\Response;
use Phoenix\Router\Request;
use Phoenix\Storage\Registry;
use Phoenix\Core\SignalSlot\Signals;
use Phoenix\Db\Factory;

class userIdnetifierPlugin extends IPlugin {

    protected $link = null;
    
    public function __construct() {
        $this->link = new Factory;
        $this->link->connect();
    }
    
    public static function __() {
        
        Manager::getInstance()->bind(Signals::SIGNAL_INIT, function() {
            if (!Request::getInstance()->getSubDomain() != FALSE | 'www'):
                $id = $this->link->select('accounts', array(
                    'id' => Request::getInstance()->getSubDomain()
                ))->fetch();
            
                if (!empty($id)):
                    if (empty(Registry::raw('IDENTITY_STORE'))):
                        Response::getInstance(Response::V11)
                        ->addHeader(
                                '301 Location: http://'.Request::getInstance()->getSubDomain().'.primemanager.biz/main/auth'
                                )
                        ->send();
                    endif;
                else:
                    Response::getInstance(Response::V11)
                    ->addHeader('301 Location: http://www.primemanager.biz/')
                    ->send();
                endif;
            endif;   
            
            
                
        });
        
        
    }
    
    
}

?>
