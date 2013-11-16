<?php

namespace Phoenix\Db;
use Phoenix\Application\Configurator;

class Factory {
    
    protected $link = null, $dsn = null;
    protected $statement = null;
    protected $result = null;
    protected $fetchMode = \PDO::FETCH_ASSOC;
    
    private $inTransaction = false;
    private $autoCommit = true;
    private $persistent = false;
    
    
    public function __construct($connection = array())
    {
        
        $this->engine = isset($connection['database-engine']) ? $connection['database-engine'] : 'mysql';
        switch($this->engine):
            case 'mysql':
            case 'pgsql':
                $this->host = isset($connection['database-hostname']) ? $connection['database-hostname'] : 'localhost';
                $this->user = isset($connection['database-username']) ? $connection['database-username'] : 'root';
                $this->pass = isset($connection['database-password']) ? $connection['database-password'] : 'root';
                $this->name = isset($connection['database-dbname']) ? $connection['database-dbname'] : 'application';
                $this->port = isset($connection['database-port']) ? $connection['database-port'] : '3306';
                
                $this->dsn = sprintf("%s:host=%s;dbname=%s;port=%s;", $this->engine, $this->host, $this->name, $this->port);
                break;
            case 'sqlite':
            case 'sqlite3':
            case 'uri':
                $this->name = isset($connection['database-dbname']) ? $connection['database-dbname'] : 'application';
                $this->dsn = sprintf("%s:%s", $this->engine, $this->name);
                break;
            case 'oci':
                $this->name = isset($connection['databse-dbname']) ? $connection['database-dbname'] : 'application';
                $this->dsn = sprintf("%s:dbname=%s", $this->engine, $this->name);
        endswitch;
        
        
        
        
        return $this;
        
    }

    public function connect()
    {
        
        if ($this->link != null)
            return;
        
        try {
        
        if (($this->engine == 'mysql') || ($this->engine == 'pgsql')) {
            $this->link = new \PDO($this->dsn, 
                $this->user, 
                $this->pass
            );
            
        } else {
            $this->link = new \PDO($this->dsn);
        }
            $this->link->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $this->link->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            if ($this->persistent == true) {
                $this->link->setAttribute(\PDO::ATTR_PERSISTENT, true);
            }
            
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        
        return $this;
    }
    
    public function prepare($sql, array $options = array())
    {
        if ($this->isConnected() == false)
            $this->connect();
        
        try {
            $this->statement = $this->link->prepare($sql, $options);
            
            return $this;
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), 500);
            return false;
        }
    }
    
    public function getStatement()
    {
        if ($this->statement == null)
            throw new \PDOException('There is no statement in use');
        
        return $this->statement;
    }
    
    public function execute(array $params = array())
    {
        if ($this->isConnected() == false)
            $this->connect();
        try {
            $this->statement->execute($params);
            
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), 500);
        }
        
        return $this;
    }
    
    public function getLastId($name = null)
    {
        try {
             $this->connect();
             $this->link->lastInsertId($name);
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }
    
    public function countAffectedRows()
    {
        try {
            return $this->getStatement()->rowCount();
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }
    
    public function fetch($style = null, $cursor = null, $offset = null)
    {
        $style = $style ? $style : $this->fetchMode;
        
        try {
            return $this->getStatement()->fetch($style, $cursor, $offset);
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }
    
    public function fetchAll($style = NULL, $column = 0)
    {
        $style = $style ? $style : $this->fetchMode;
        
        try {
            return $style === \PDO::FETCH_COLUMN ?
                $this->getStatement()->fetchAll($style, $column) :
                $this->getStatement()->fetchAll($style);
            
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }
    
    public function insert($table, array $bind = array())
    {
        if ($this->isConnected() == false)
            $this->connect();
        
        $cols = implode(', ', array_keys($bind));
        $params = implode(', :', array_keys($bind));
        
        foreach($bind as $col => $val):
            unset($bind[$col]);
            $bind[':'.$col] = $val;
        endforeach;
        
        $query = "INSERT INTO $table ($cols) VALUES (:$params);";
        
        return (int) $this->prepare($query)
            ->execute($bind)
            ->getLastId();
    }
    
    public function update($table, array $bind, array $conditions = array())
    {
    	
        if ($this->isConnected() == false) {
            $this->connect();
        }
        
        $set = array();
        
        foreach($bind as $col => $val) {
            unset($bind[$col]);
            $bind[':'.$col] = $val;
            $set[] = $col.'= :'.$col;
        }
        
        if (is_array($conditions) && !empty($conditions)) {
            foreach($conditions as $k => $v) {
                $condition[] = $k.'= :'. $k;
                $bind[':'.$k] = $v;
            }
            
            $where = implode(' AND ', $condition);
        }
        
        $query = 'UPDATE '.$table.' SET '.implode(', ', $set) .
            (isset($where) ? ' WHERE '.$where : '').';';
        
        return (int) $this->prepare($query)
                    ->execute($bind)
                    ->countAffectedRows();
    }
    
    public function select($table, array $bind = array(), $limit = null, $operator = 'AND')
    {
        if ($this->isConnected() == false)
            $this->connect();
        $where = array();
        if ($bind && is_array($bind)):
            
            foreach($bind as $col => $val):
                unset($bind[$col]);
                $bind[':'.$col] = $val;
                $where[] = $col .' = :'.$col;
            endforeach;
        endif;
        
        $query = "SELECT * FROM $table " .
                (($where) ? ' WHERE ' . implode(' ' . $operator . ' ', $where) : '').
                (($limit) ? 'LIMIT '.$limit : '') .
                ';';
                
        $this->prepare($query)
                ->execute($bind);
        
        return $this;
    }
    
    public function delete($table, $where = '')
    {
        if ($this->isConnected() == false)
            $this->connect();
        $query = "DELETE FROM $table".
                (($where) ? 'WHERE ' . $where : '').';';
        
        return $this->prepare($query)
                    ->execute()
                    ->countAffectedRows();
        
    }
    
    public function beginTransaction()
    {
        try {
            $this->link->beginTransaction();
            $this->link->setAttribute(\PDO::ATTR_AUTOCOMMIT, $this->autoCommit);
            $this->inTransaction = true;
        } catch(\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        return $this;
    }
    
    public function commit()
    {
        if ($this->inTransaction != false):
            try {
                $this->link->commit();
                $this->inTransaction = false;
            } catch (\PDOException $e) {
                throw new \RuntimeException($e->getMessage(), $e->getCode(), $e->getPrevious());
            }
        endif;
        
        return $this;
    }
    
    public function rollBack()
    {
        if ($this->inTransaction != false):
        try {
            $this->link->rollBack();
            $this->inTransaction = false;
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
        endif;
        
        return $this;
    }
    
    public function setAutoCommit($enabled = false)
    {
        $this->autoCommit = $enabled;
        
        return $this;
    }
    
    public function disconnect()
    {
        return $this->link->close($this->link);
        return null;
    }
    
    public function isConnected()
    {
        try {
            return (bool) $this->link->query('SELECT 1+1');
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e->getPrevious());
            $this->link = null;
            return false;
        }
    }
    
    public function hasError()
    {
        if ($this->getStatement()->errorCode() == '00000' | '')
            return false;
        else
            return true;
    }
    
    public function setPersistent($state)
    {
        $this->persistent = $state;
        
        return $this;
    }
    
    public function __sleep()
    {
        return array(
        	$this->dsn
        );
    }
    
    
    public function __wakeup()
    {
        $this->connect();
    }
    
    
}

?>