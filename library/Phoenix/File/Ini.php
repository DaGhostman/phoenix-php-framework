<?php
namespace Phoenix\File;

class Ini {
    
    private $contents;
    public function __construct($filepath, $parse = true)
    {
        if (is_file($filepath) & is_readable($filepath)):
            $content = parse_ini_file($filepath, $parse);
            $this->contents = $this->toObject($content);
        endif;
        
        return $this;
    }
    
    protected function toObject($array)
    {
        if (is_array($array))
        {
            $obj = new \stdClass();
            foreach($array as $key => $value)
            {
                $obj->$key = $this->toObject($value);
            }
            
            return $obj;
        } else {
            return $array;
        }
    }
    
    public function __get($key)
    {
        return $this->contents->$key;
    }
    
    public function __set($key, $value)
    {
        $this->contents->$key = $value;
    }
}
?>