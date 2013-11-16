<?php 
namespace Phoenix\Service\OAuth;

class Provider {
    
    protected $_object = null,
                $_store = null,
                $error = false;
    
    final public function __construct($storage)
    {
        if (!is_object($storage) || $storage == null) {
            throw new \Exception('No storage engine specified.', 500);
        }
        
        $this->_store = $storage;
        
        $this->_object = new \OAuthProvider();
        
        
        $this->_object->consumerHandler(array($this, 'consumerHandler'));
        $this->_object->tokenHandler(array($this, 'tokenHandler'));
        $this->_object->timestampNonceHandler(array($this, 'nonceHandler'));
    }
    
    public function checkRequest() {
        try {
            $this->_object->checkOAuthRequest();
        } catch(\OAuthException $e) {
            echo $this->_object->reportProblem($e);
            $this->error = true;
        }
        
        return $this;
    }
    
    public function requestTokenQuery() {
        $this->_object->isRequestTokenEndpoint(true);
        $this->_object->addParameter('oauth_callback');
        
        return $this;
    }
    
    public function generateRequestToken($t_length, $s_length, $strong = true)
    {
        if ($this->error) {
            return false;
        }
        
        $token = sha1($this->_object->generateToken($t_length, $strong));
        $secret = sha1($this->_object->generateToken($s_length, $strong));
        
        $callback = $this->_object->callback;
        
        // @TODO: Creation of tokens
        
        return 'oauth_token=' . $token . 
        		'&oauth_token_secret=' . $secret . 
        		'&oauth_callback_confirmed=true';
    }
    
    public function generateAccessToken($t_length, $s_length, $strong = true)
    {
        if ($this->error) {
            return false;
        }
        
        $token = sha1($this->_object->generateToken($t_length, $strong));
        $secret = sha1($this->_object->generateToken($s_length, $strong));
        
        //@TODO: Updating tokens in the store
    }
    
    public function consumerHandler($provider){
        $consumer = $this->_store->fetchConsumerByKey($provider->consumer_key);
        
        if (is_object($consumer)) {
            if ($consumer->isRevoked()) {
                return OAUTH_CONSUMER_KEY_REFUSED;
            }
            
            $this->_consumer = $consumer;
            $provider->consumer_secret = $this->consumer->consumer_secret;
            
            return OAUTH_OK;
        }
        
        return OAUTH_CONSUMER_KEY_UNKNOWN;
    }
    public function tokenHandler($provider) {
        $token = $this->_store->findTokenByKey($provider->token);
        if(is_null($token)){ 
            return OAUTH_TOKEN_REJECTED;
        } elseif($token->getType() == 1 && $token->getVerifier() != $provider->verifier){ // bad verifier for request token
            return OAUTH_VERIFIER_INVALID;
        } else {
            if($token->getType() == 2){
            /* if this is an access token we register the user to the provider for use in our api */
                $this->user = $token->getUser();
            }
            $provider->token_secret = $token->getSecret();
            return OAUTH_OK;
        }
    }
    abstract public function timestampTokenHandler();
    
    public function getObject() {
        return $this->_object;
    }
}


?>