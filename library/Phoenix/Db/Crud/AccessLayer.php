<?php

namespace Phoenix\Db\Crud;


abstract class AccessLayer {
    
    
    private $adapter = null;
    public $entityTable = null;
    private $entities = array();
    
    protected $_idColumn = 'id';
    
    final public function __construct($adapter)
    {
        if (!is_object($adapter)) {
            throw new \RuntimeException('The argument should be an object');
        }
        
        $methods = get_class_methods($adapter);
        
        if (in_array('isConnected', $methods)) {
            if ($adapter->isConnected() != true) {
                throw new \RunetimeExcetion('The adapter is not connected');
            }
        }
        
        $this->adapter = $adapter;
    }
    
    final public function getAdapter()
    {   
        return $this->adapter;
    }
    
    final public function setEntityTable($name)
    {
        $this->entityTable = $name;
        
        return $this;
    }
    
    
    final public function findById($id)
    {
        $this->getAdapter()->select($this->entityTable, array($this->_idColumn => $id));
        
        if (!$row = $this->getAdapter()->fetch())
            return null;
        else 
            return $this->createEntity($row);
    }
    
    
    final public function findAll(array $conditions = array())
    {
        $this->entities = array();
        
        $this->getAdapter()->select($this->entityTable, $conditions);
        $rows = $this->getAdapter()->fetchAll();
        
        if($rows):
            foreach ($rows as $row):
                $this->entities[] = $this->createEntity($row);
            endforeach;
        endif;
        
        return $this->entities;
    }
    
    
    final public function insert(array $bind = array())
    {
        $this->getAdapter()->insert($this->entityTable, $bind);
    }
    
    final public function update(array $bind, array $conditions)
    {
        
        $this->getAdapter()->update($this->entityTable, $bind, $conditions);
        
        return $this;
    }
    
    final public function setIdColumn($columnName)
    {
        $this->_idColumn = $columnName;
        
        return $this;
    }

    abstract public function createEntity(array $row);
    
}

?>