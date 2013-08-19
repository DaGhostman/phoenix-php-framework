<?php

namespace Phoenix\Db\Orm;

use Phoenix\Db\Factory;


abstract class AccessLayer {
    
    
    private $adapter = null;
    public $entityTable = null;
    private $entities = array();
    
    protected $_idColumn = 'id';
    
    final public function __construct(Factory $adapter)
    {
        $this->adapter = $adapter;
    }
    
    final public function getAdapter()
    {
        if (!$this->adapter instanceof Factory)
            throw new \RuntimeException('Invalid DB adapter specified');
        
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