<?php

namespace Phoenix\Core;
use Phoenix\Application\Core;
use Phoenix\Core\SignalSlot\Manager;
use Phoenix\Core\SignalSlot\Signals;

class Handler {
    public static function error_handler($errno, $errstr, $errfile, $errline){
        Core::getInstance();
        Manager::getInstance()->emit(Signals::SIGNAL_ERROR);
        
        $error_string = '';
        $error_string .= '=====['.date("d:m:Y H:i:s").']====='.PHP_EOL;
        $error_string .= 'Error Occured in File: ' . $errfile. ' on line: ' . $errline . PHP_EOL;
        $error_string .= 'Message: ' . $errstr . PHP_EOL;
        $error_string .= 'Code: ' . $errno . PHP_EOL;
        $error_string .= '---------------' . PHP_EOL . PHP_EOL;
        
        Core::writelog('Errors.log',$error_string);
    }
    public static function exception_handler($e){
        Core::getInstance();
        Manager::getInstance()->emit(Signals::SIGNAL_EXCEPTION);
        
        if ($e != null | false) {
        $error_string = '=====[' . date("d:m:Y H:i:s") . ']=====' . PHP_EOL;
        $error_string .= 'Unhandled Exception occured in: ' . $e->getFile() . PHP_EOL;
        $error_string .= 'Error Line: ' . $e->getLine() . PHP_EOL;
        $error_string .= 'Message: ' . $e->getMessage() . PHP_EOL;
        $error_string .= 'Code: ' . $e->getCode() . PHP_EOL;
        $error_string .= '---------- BEGIN TRACE '. PHP_EOL;
        $i = 0;
        foreach($e->getTrace() as $trace)
        {
            $error_string .= $i . ' => ' . $trace['file'] . ':' . $trace['line'] . PHP_EOL;
            $i++;
        }
        $error_string .= '---------- END TRACE '. PHP_EOL;
        
        Core::writelog('Exceptions.log', $error_string);
        }
    }
}

?>