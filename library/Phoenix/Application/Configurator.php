<?php
namespace Phoenix\Application;

class Configurator implements \ArrayAccess, \IteratorAggregate {
    protected $_fileObject = null;
    protected $_nsDelimiter = '-';
    
    public function setDelimiter($del) {
        $this->_nsDelimiter = $del;
        
        return $this;
    }
    
    public function setFile($object) {
        if (!is_object($object) || (!$object instanceof \ArrayAccess)) {
            throw new \InvalidArgumenException('
                The supplied argument should be an object and implement ArrayAccess
            ');
        }
        
        $this->_fileObject = $object;
        
        return $this;
    }
    
    public function __construct($object = null){
        if ($object != null) {
            $this->setFile($object);
        }
    }
    
    /**
     * 
     * Protected method used to strip the 
     * @param unknown_type $offset
     */
    protected function stripNamespace($offset) {
        list($namespace, $option)=preg_split('#'.$this->_nsDelimiter.'#i', $offset);
    
        return $namespace;
    }
    
    protected function stripOption($offset) {
        list($namespace, $option)=preg_split('#'.$this->_nsDelimiter.'#i', $offset);
    
        return $option;
    }

    /**
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset) {
        $namespace = $this->stripNamespace($offset);
        $option = $this->stripOption($offset);
        
        
        if (!$this->offsetExists($offset)) {
            throw new \InvalidArgumentException(sprintf('
            The namespace \'%s\' or option \'%s\' are not found in the configuration.
            ', $namespace, $option));
        }
        
        return $this->_fileObject[$namespace][$option];
    }
    
    /**
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset) {
        $namespace = $this->stripNamespace($offset);
        $option = $this->stripOption($offset);
        
        if (!$this->_fileObject->offsetExists($namespace)) {
            return false;
        } 
        
        if (!isset($this->_fileObject[$namespace][$option])) {
            return false;
        }
        
        return true;
    }
    
    public function offsetSet($offset, $value) {}
    
    /**
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset) {
        if ($this->offsetExists($offset) == true) {
            
            $namespace = $this->stripNamespace($offset);
            $option = $this->stripOption($offset);
            
            unset($this->_fileObject[$namespace][$option]);
        }
    }
    
    /**
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator() {
        return new \ArrayIterator($this);
    }
}

?>
