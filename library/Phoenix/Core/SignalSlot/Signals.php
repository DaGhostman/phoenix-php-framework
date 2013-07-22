<?php

/**
 * 
 * @author Dimitar Dimitrov <daghostman.dd@gmail.com>
 * @link http://web-forge.org/
 * @copyright (c) 2013, Dimitar Dimitrov
 * @license http://creativecommons.org/licenses/by-sa/3.0/ Attribution-ShareAlike 3.0 Unported
 * 
 */

namespace Phoenix\Core\SignalSlot;

class Signals {

    // System Signals
    const SIGNAL_INIT = 9001,
            SIGNAL_BOOTSTRAP = 9002,
            SIGNAL_RUN = 9003,
            SIGNAL_SHUTDOWN = 9004,
            SIGNAL_KILL = 9005,
            SIGNAL_ERROR = 9006,
            SIGNAL_EXCEPTION = 9007;
    
    // Routing Signals
    const SIGNAL_ENROUTE = 9101,
            SIGNAL_DISPATCH = 9102,
            SIGNAL_TRIGGER = 9103,
            SIGNAL_RESPONSE = 9104,
            SIGNAL_DISPATCH_ERR = 9105;
    
    // Plugin Signals
    const SIGNAL_PLUGIN_INIT = 9201,
            SIGNAL_PLUGIN_START = 9202,
            SIGNAL_PLUGIN_X = 9203,
            SIGNAL_PLUGIN_ERR = 9204;
    
    // DB Signals
    const SIGNAL_DB_CONNECT = 9301,
            SIGNAL_DB_EXECUTE = 9302,
            SIGNAL_DB_COMMIT = 9303,
            SIGNAL_DB_ROLLBACK = 9304,
            SIGNAL_DB_DISCONNECT = 9305;
    
    // Stream Signals
    const SIGNAL_STREAM_REGISTER = 9401,
            SIGNAL_STREAM_UNREGISTER = 9402;

    // Auth Signals
    const SIGNAL_AUTH_SUCCESS = 9501,
            SIGNAL_AUTH_FAIL = 9502,
            SIGNAL_AUTH_SAVE_DB = 9503,
            SIGNAL_AUTH_SAVE_REG = 9504,
            SIGNAL_AUTH_SAVE_SESS = 9505,
            SIGNAL_AUTH_SAVE_ALL = 9506;
    
    // Cache Signals
    const SIGNAL_CACHE_STORE = 9601,
            SIGNAL_CACHE_FETCH = 9602,
            SIGNAL_CACHE_UPDATE= 9603,
            SIGNAL_CACHE_CLEAR = 9604;
}

?>
