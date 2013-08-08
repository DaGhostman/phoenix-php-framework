<?php
namespace Phoenix\File;

class Json implements \Serializable {
    
    private $contents;
    public function __construct($filepath, $parse = true)
    {
        if (is_file($filepath) & is_readable($filepath)):
            $content = file_get_contents($filepath);
            $this->contents = $this->toObject(json_decode($content, $parse));
        endif;
        
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
    
    public function __toString()
    {
        return json_encode($this->contents);
    }
    
    public function serialize()
    {
        return serialize($this->contents);
    }
    
    public function unserialize($content)
    {
        return unserialize($content);
    }
    
    public function __get($key)
    {
        var_dump($key);
        return $this->contents->$key;
    }
    
    public function __set($key, $value)
    {
        $this->contents->$key = $value;
    }
}

?>