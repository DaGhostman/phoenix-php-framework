<?php

namespace Phoenix\File;

class Cvs {
    
    const DEFAULT_DELIMITER = ',';
    const DEFAULT_ENCLUSURE = '"';
    const DEFAULT_ESCAPE = '\\';


    protected $fp = null;
    protected $data = array();
    
    public function __construct($filepath)
    {
        if (is_file($filepath) & is_readable($filepath)):
            $this->fp = file($filepath, FILE_IGNORE_NEW_LINES);
        else:
            throw new \InvalidArgumentException("Unable to open CVS file");
        endif;
    }
    
    public function parse($delimiter, $encapsulate)
    {
        
        foreach($this->fp as $num => $line):
            foreach (explode(($delimiter ? $delimiter : self::DEFAULT_DELIMITER), $line) as $key => $value):
                $this->data[$num][$key] = trim(str_replace(($encapsulate ? $encapsulate : self::DEFAULT_ENCLUSURE), '', $line));
            endforeach;
        endforeach;
        
        return $this;
    }
    
    public function fetch()
    {
        if (!empty($this->data)):
            return $this->data;
        else:
            return false;
        endif;
    }
}


?>