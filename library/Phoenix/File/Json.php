<?php
namespace Phoenix\File;

class Json implements \ArrayAccess, \IteratorAggregate {
    
    private $contents;
    public function __construct($filepath)
    {
        if (!is_file($filepath) & !is_readable($filepath)):
            throw new \InvalidArgumentException(sprintf('
            The file \'%s\' does not exist or its not readable.
            '));
        endif;
        
        
        $content = file_get_contents($filepath);
        $this->contents = json_decode($content, true);
    }
    
    public function __toString()
    {
        return json_encode($this->contents);
    }
    
    public function __get($key)
    {
        return $this->contents[$key];
    }

public function offsetExists($offset) {
        return array_key_exists($offset, $this->contents);
    }

    public function offsetGet($offset) {
        return $this->contents[$offset];
    }

    public function offsetSet($offset, $value) {}
    
    public function getIterator() {
        return new \ArrayIterator($this->contents);
    }

    public function offsetUnset($offset) {
        unset($this->contents[$offset]);
    }
}

?>