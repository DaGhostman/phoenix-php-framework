<?php

namespace Phoenix\Application;

use Phoenix\Core\SignalSlot\Manager;
use Phoenix\Core\SignalSlot\Signals;

class Core {
    
    private static $_instance = null;
    
    protected static $_objectStorage = array();
    
    protected static $_fp = null;
    
    public static function getInstance()
    {
        if (self::$_instance == null | !self::$_instance instanceof Core)
            self::$_instance = new Core;

        return self::$_instance;
    }
    
    ### Observer/Subject pattern ###
    ###### BEGIN ######
    
    public static function attach($subject, $observer)
    {
        self::$_objectStorage[$subject][] = $observer;
        self::$_objectStorage[$subject]['state'] = 'idle';
    }
    
    public static function detach($subject, $observer)
    {
        $key = array_search($observer, self::$_objectStorage[$subject], TRUE);
        unset(self::$_objectStorage[$subject][$key]);
    }
    
    public static function setState($state, $subject)
    {
        self::$_objectStorage[$subject]['state'] = $state;
    }
    
    public static function notify($subject)
    {
        if (array_key_exists($subject, self::$_objectStorage) & !empty(self::$_objectStorage[$subject]))
        {
            $state = self::$_objectStorage[$subject]['state'];
            unset(self::$_objectStorage[$subject]['state']);
            foreach (self::$_objectStorage as $observer)
            {
                if (method_exists($observer, $state))
                    $observer->$state();
            }
        }
    }

    ###### END ######
    #################
    #### Logging ####
    ##### BEGIN #####

    public static function writelog($logfile, $message)
    {
        if (!in_array('log', stream_get_wrappers())) {
            Manager::getInstance()->emit(Signals::SIGNAL_STREAM_REGISTER, 
                    array("stream" => "log", 
                        "handler" => "Phoenix\Core\Streams\LogStream")
                    );
            stream_register_wrapper("log", 
                    "Phoenix\Core\Streams\LogStream");
            
        }
        
        
        self::$_fp = fopen("log://{$logfile}", "ab");
        $len = strlen($message . PHP_EOL) + 1;
        fwrite(self::$_fp, $message . PHP_EOL, $len);
        fclose(self::$_fp);
    }
    
    ###### END ######
    #################
    #### PLUGINS ####
    ##### BEGIN #####
    
    


}


?>