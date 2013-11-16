<?php 

namespace Phoenix\Service\OAuth\Consumers;

class DefaultConsumer {
    private $_object = null,
                $_key = null,
                $_secret = NULL,
                $_tokenUrl = null,
                $_accessUrl = null,
                $_authUrl = null,
                $_config = null,
                $_requestToken = null,
                $_accessToken = null;
                
    
    public function __construct($config) {
        
        $this->_key = $config['oauth-consumer.key'];
        $this->_secret = $config['oauth-consumer.secret'];
        $this->_accessUrl = $config['oauth-consumer.request.access'];
        $this->_authUrl = $config['oauth-consumer.request.auth'];
        $this->_tokenUrl = $config['oauth-consumer.request.token'];
        
        $this->_config = $config;
        
        $this->_object = new \OAuth($this->_key, 
                            $this->_secret, 
                            $config['oauth-consumer.method'], 
                            $config['oauth-consumer.type']);
    }
    
    public function getRequestToken() {
        try {
            $this->_object->setAuthType(OAUTH_AUTH_TYPE_FORM);
            $this->_requestToken = $this->_object->getRequestToken($this->_tokenUrl, 'oob');
            $this->_object->setToken($this->_requestToken['oauth_token'], 
                                        $this->_requestToken['oauth_token_secret']);
        } catch (\OAuthException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }
    
    public function setNonce($nonce) {
        try {
            if ($this->_object->setNonce($nonce)) {
                return $this;
            }
        } catch (\OAuthException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }
    
    public function getAccessToken() {
        try {
            $this->_object->setToken($this->_requestToken['oauth_token'], $this->_requestToken['oauth_token_secret']);
        
            $this->_accessToken = $this->_object->getAccessToken($this->_accessUrl);
            $this->_object->setToken($this->_accessToken['oauth_token'], $this->_accessToken['oauth_token_secret']);
        } catch (\OAuthException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }

    public function generateTimestamp() {
        return time();
    }
    
    public function fetch($url, $params = array(), $method = 'POST', $headers = array()) {
        try {
            $this->_object->setTimestamp(time());
            $signature = $this->_object->generateSignature($method, $url, $params);
            
            $parameters['oauth_signature'] = $signature;
            $res = $this->_object->fetch($url, $params, $method, $headers);
            if ($res) {
                return $this->_object->getLastResponse();
            }
        } catch (\OAuthException $e) {
            throw new \Exception($e->getMessage(), 500);
        }
    }

}



?>