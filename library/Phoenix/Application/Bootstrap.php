<?php 

namespace Phoenix\Application;

class Bootstrap {
    protected $_dic = null;
    
    final public function setDiContainer($container){}
    final public function getDiContainer(){}
    
    final public function __destruct() {
        $di = $this->getDiContainer();
        
        if (!array_key_exists('configuration', $di)) {
            throw new \Exception('
            There should be a method, defining \'configuration\' entry 
            inside the DI.
            ');
        }
    }
}

?>