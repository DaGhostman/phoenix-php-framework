<?php 
namespace Phoenix\Service\OAuth\Consumers;

class TwoLegConsumer {
    private $_object = null,
                $_key = null, # Consumer Key
                $_secret = NULL, # Consumer Secret
                $_method = null, # OAuth Method
                $_type = null, # OAuth Type
                $_tokenUrl = null, # Request Token URL
                $_accessUrl = null, # Access Token URL
                $_callbackUrl = null, # Callback URL
                $_config = null; # Config Object
                
    protected $_requestToken, $_requestTokenSecret;
    
    public function __construct($config) {
        
        $this->_key = $config['oauth-consumer.key'];
        $this->_secret = $config['oauth-consumer.secret'];
        $this->_method = $config['oauth-consumer.method'];
        $this->_type = $config['oauth-consumer.type'];
        
        $this->_config = $config;
        
        $this->_object = new \OAuth($this->_key, 
                            $this->_secret, 
                            $this->_method, 
                            $this->_type);
                            
    }
    
    
    public function getAccessToken($type = OAUTH_AUTH_TYPE_URI) {
        try {
            $this->_object->setAuthType($type);
            return $this->_object->getRequestToken($this->_tokenUrl, $this->_callbackUrl);
        } catch (\OAuthException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }
    
    
    public function generateNonce($length = 32) {
        try {
            $nonce = '';
            $allowed = array_merge(range('0','9'), range('a','z'), range('A','Z'));
            
            for($i=0; $i < $length; $i++) {
                $nonce .= $allowed[array_rand($allowed)];
            }
            
            $this->_object->setNonce($nonce);
        } catch (\OAuthException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
        
        return $this;
    }
    
    
    public function getAccessToken($token, $secret) {
        /**
         * @TODO: Request $token and $secret
         */
        
        try {
            $this->_object->setToken($token, $secret);
            return $this->_object->getAccessToken($this->_accessUrl);
        } catch (\OAuthException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }
    
    
    public function fetch($url, $token, $secret) {
        try {
            $this->_object->setToken($token, $secret);
            
            $res = $this->_object->fetch($url);
            if ($res) {
                return $this->_object->getLastResponse();
            }
        } catch (\OAuthException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }
}

?>