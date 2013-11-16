<?php 
namespace Phoenix\Service\OAuth;
use Phoenix\Service\OAuth\Consumers\DefaultConsumer;

class Consumer {
    
    const DEF_CONSUMER = 0;
    
    protected $__instance = null;
    
    public function __construct($consumer, $config) {
        switch($consumer) {
            case self::DEF_CONSUMER:
            case 'DefaultConsumer':
            default:
                
                if (!in_array('OAuth', get_loaded_extensions()))
                    throw new \Exception('OAuth extension is not loaded.', 500);
                
                $this->__instance = new DefaultConsumer($config);
                break;
        }
    }
    
    
    public function getConsumer() {
        return $this->__instance;
    }
}

?>