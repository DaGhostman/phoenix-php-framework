<?php
namespace Phoenix\File;

class Ini implements \ArrayAccess {
    
    private $contents, $raw;
    public function __construct($filepath, $parse = true)
    {
        if (is_file($filepath) & is_readable($filepath)):
            $content = parse_ini_file($filepath, $parse);
            $this->raw = $content;
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
    
    public function raw()
    {
        return $this->raw;
    }
    
    public function __set($key, $value)
    {
        $this->contents->$key = $value;
    }

    public function offsetExists($offset) {
        return array_key_exists($offset, $this->raw);
    }

    public function offsetGet($offset) {
        return $this->raw[$offset];
    }

    public function offsetSet($offset, $value) {
        $this->raw[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset($this->raw[$offset]);
    }
}
?>