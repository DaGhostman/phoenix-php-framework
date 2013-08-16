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
namespace Phoenix\Services\WebSockets;
use Phoenix\Application\Core;

class Server {

    protected $hostname = '',
            $port = 8080,
            $_socket = null,
            $socketStorage = array();
    
    
    protected static $_instance = null;
            
    private function __construct($hostname, $port = 8080) {
        $this->hostname = $hostname;
        $this->port = $port;
        if (extension_loaded('sockets')):
            Core::writelog('sockets', 'Initing Sockets : '. $hostname.':'.$port);
            $this->_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            socket_set_option($this->_socket, SOL_SOCKET, SO_REUSEADDR, 1);
            socket_bind($this->_socket, $this->hostname, $this->port);
            socket_listen($this->_socket, 5);
            
            $this->socketStorage[] = $this->_socket;
            
            while (true):
                $changed = $this->socketStorage;
                socket_select($changed, NULL, NULL, NULL);
                foreach ($changed as $sock):
                    if ($sock === $this->_socket):
                        $client = socket_accept($sock);
                        if ($client<0): Core::writelog ('sockets', 
                                'Failed to accept the socket @ ' . 
                                $this->hostname.':'.$this->port);
                        else:
                            $this->connect($sock);
                        endif;
                        
                    else:
                        $bytes = @socket_recv($sock, $buffer, 2048, 0);
                        if ($bytes==0)
                            $this->disconnect($sock);
                        else {
                            $user = $this->getUserBySock($sock);
                            if (!$user->handshake) {$this->handshake($user, $buffer);}
                            else {$this->process($user, $this->unwrap($buffer));}
                        }
                    endif;
                endforeach;
            endwhile;
        else:
            throw new \RuntimeException(
                    'PHP Sockets Extension is not loaded. Please check your php.ini settings.'
                    );
        endif;
    }
    
    public function process($user, $msg){}
    
    public static function getInstance($host, $port) {
        if (self::$_instance == null || !self::$_instance instanceof Server) {
            self::$_instance = new Server($host, $port);
        }
        
        return $this;
    }

}

?>
