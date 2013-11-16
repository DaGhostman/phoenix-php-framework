<?php 

namespace Phoenix\Core\Di;

class Container implements \ArrayAccess, \Serializable, \Countable, \IteratorAggregate {
    protected $_dStorage = array();
    
    /**
     * Bootstrap injection of assoc array, that contains
     * defined injections, which are to be used later.
     * 
     * @param array $values Array of injections to store in key=>value pairs
     */
    public function __construct($values) {
        $this->_dStorage = $values;
    }
    
    /**
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator() {
        return new \ArrayIterator($this->_dStorage);
    }
    
    /**
     * @see Serializable::serialize()
     */
    public function serialize() {
        return serialize($this->_dStorage);
    }
    
    /**
     * @see Serializable::unserialize()
     */
    public function unserialize($serialized) {
        return unserialize($serialized);
    }
    
    /**
     * @see Countable::count()
     */
    public function count() {
        return count($this->_dStorage);
    }
    
    /**
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->_dStorage);
    }
    
    /**
     * @throws \UnexpectedValueException when key does not exist.
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset) {
        if ($this->offsetExists($offset)) {
            $extended = (is_object($this->_dStorage[$offset]) && 
                            method_exists($this->_dStorage[$offset], '__invoke')
                        );
                        
            
            switch ($extended) {
                case true:
                    return $this->_dStorage[$offset]($this);
                    break;
                default:
                    return $this->_dStorage[$offset];
                    break;
            }
        }
        
        throw new \UnexpectedValueException(sprintf("
            The required key \"%s\" was not found in the container.
        "));
        return null;
    }
    
    /**
     * @param string $offset Key id to access the injection
     * @param callable $value The Closure object injection
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value) {
        
        if (!is_object($closure) || !method_exists($closure, '__invoke')) {
            throw new \InvalidArgumentException(sprintf('
            	The argument passed to Di\Container::attach(); 
            	with key \'%s\' should be callable.', 
                $key));
        }
        
        if ($this->offsetExists($key)) {
            throw new \LogicException(sprintf('
            	The argument passed to Di\Container::attach(); 
            	with key \'%s\' already exists. See Di\Container::update();.', 
                $key));
        }
        
        $this->_dStorage[$offset] = $value;
    }
    
    /**
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset) {
        if ($this->offsetExists($offset)) {
            unset($this->_dStorage[$offset]);
        }
        
        return;
    }
    
    /**
     * 
     * Enter description here ...
     * @param string $key_id textual id to access the injection
     * @param string $closure Closure to
     */
    public function еxtend($key_id, $closure) {
        if (!array_key_exists($key_id, $this->_dStorage)) {
            throw new \LogicException(sprintf('
            	The argument passed to Di\Container::attach(); 
            	with key \'%s\' does not exist.', 
                $key));
        }
        
        $extendable = $this->_dStorage[$key_id];
        
        $this->_dStorage[$key_id] = function($arg) use ($closure, $extendable) {
            return $closure($extendable($arg), $arg);
        };
    }
}

?>