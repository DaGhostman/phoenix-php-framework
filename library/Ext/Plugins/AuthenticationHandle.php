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

namespace Ext\Plugins;
use Phoenix\Plugin\IPlugin;
use Phoenix\Core\SignalSlot\Manager;
use Phoenix\Core\SignalSlot\Signals;
use Phoenix\Db;


class AuthenticationHandle extends IPlugin {

    public function __construct() {
        
    }

    public static function __() {
        
        Manager::getInstance()->bind(Signals::SIGNAL_RUN, function() {
            $db = new Db\Factory();
            $db->connect();
            $access = new AuthModel($db);
            $access->entityTable('accounts');
            $this->___auth = new Auth\Factory(Auth\Factory::AUTH_DB, &$access);
        
            if (!$this->___auth->getIdentity(array(), 'session') && $_POST) {
                $this->___auth
                    ->setIdentity($_POST['username'], 'username')
                    ->setCredential($_POST['password'], 'password')
                    ->setTokenField('session_id')
                    ->authenticate();
            }
        
        
            if (!$this->___auth->hasIdentity()) {
                $access->entityTable(Request::getInstance()->getSubDomain().'_employees');
                $this->___auth->authenticate();
            }
            
            $this->___auth->saveIdentity('session');
        });
        
        
    }

}

?>
