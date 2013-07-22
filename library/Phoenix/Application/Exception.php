<?php

namespace Phoenix\Application;

class Exception extends \Exception {
    
    public function __toString()
    {
        $line     = $this->getLine();
        $file     = $this->getFile();
        $trace    = $this->getTrace();
        $code     = $this->getCode();
        $message  = $this->getMessage();
        $prev     = $this->getPrevious();
        
        $e = 'Forge\Application\Exception thrown at line:';
        $e .= $line.'</strong> in <strong>'.$file;
        $e .= '</strong><br />';
        $e .= 'Message <strong>:'.$message.'<strong> code: <strong>' . $code . '</strong><br />';
        $e .= 'Stack Trace:<br />';
        
        foreach ($trace as $key => $value):
            $e .= '['.$key.'] =><br />';
            $e .= '&nbsp;&nbsp;&nbsp;&nbsp;File: <strong>'.$value['file'].'</strong><br />';
            $e .= '&nbsp;&nbsp;&nbsp;&nbsp;Line: <strong>'.$value['line'].'</strong><br />';
            $e .= '&nbsp;&nbsp;&nbsp;&nbsp;In: <strong>'.$value['class'].'::'.$value['function'].'</strong><br />';
            $e .= '&nbsp;&nbsp;&nbsp;&nbsp;Arguments: <strong>'.print_r($value['args'], true).'</strong><br />';
        endforeach;
        
        $e .= '&nbsp;&nbsp;&nbsp;&nbsp;<strong>Previous</strong>: '.print_r($prev, true);
        
        return $e;
    }
    
    
}

?>