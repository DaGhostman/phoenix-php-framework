<?php

namespace Phoenix\Router;

class Request {
    
    const REQ_GET = 'GET';
    const REQ_POST = 'POST';
    const REQ_PUT = 'PUT';
    const REQ_DELETE = 'DELETE';
    const REQ_HEAD = 'HEAD';
    
    protected $uri,
            $params = array(), 
            $serverName,
            $domainComponents,
            $urlComponents;
    
    private static $instance = null;
    
    public static function getInstance($uri = null, $params = null)
    {
        if (!self::$instance) self::$instance = new Request($uri, $params);
    
        return self::$instance;
    }
    
    /**
     * Creates the request object
     * 
     * @param string $uri Recomended the result from the $_SERVER['REQUEST_URI']
     * @param array $params Array with the parameters in the request
     * @throws \InvalidArgumentException
     * @return \Forge\Router\Request
     */
    private function __construct($uri, $params = array())
    {
        $params = !empty($params) ? $params : array();
        
        $url = ($_SERVER['SERVER_PORT'] == 80 ? 'http://' : 'https://') . 
                    $_SERVER['SERVER_NAME'];
        
        if(!filter_var($url.$uri, FILTER_VALIDATE_URL))
        {
            throw new \InvalidArgumentException('Invalid URL');
        }
        // The protocol, user, password, host
        $x = parse_url($url.$uri);
        $this->urlComponents = $x;
        $this->serverName = $x['host'];
        $this->domainComponents = array();
        
        if (preg_match_all('/\./i', $this->serverName, $matches) === 2):
                list(
                $this->domainComponents['subdomain'], 
                $this->domainComponents['domain'], 
                $this->domainComponents['tld'])=  explode('.', $this->serverName);
        endif;
        
        if (preg_match_all('/\./i', $this->serverName, $matches) === 1):
                $this->domainComponents['subdomain'] = null;
                list(
                $this->domainComponents['domain'], 
                $this->domainComponents['tld'])=  explode('.', $this->serverName);
        endif;
        
        foreach($params as $key => $value)
        {
            $this->setParams($key, $value);
        }
        
        $this->uri = $uri;
        
        return $this;
    }
    
    /**
     * Returns the current request URI
     * 
     * @return string Containing he current request URI
     */
    public function getUri()
    {
        return $this->uri;
    }
    
    /**
     * Returns the server name for the current request
     * 
     * @return string The server name which handles the current request
     */
    
    public function getServerName()
    {
        return $this->serverName;
    }
    
    /**
     * Returns the subdomain of the current request
     * 
     * @return string The subdomain of the request
     */
    public function getSubDomain()
    { 
        if (array_key_exists('subdomain', $this->domainComponents))
            return $this->domainComponents['subdomain'];
        else return '';
    }
    
    public function __call($name, $args = array()) {
        if (substr(strtolower($name), 0, 3) === 'get')
                return $this->get(substr($name, 3), 'param');
        
        if (substr(strtolower($name), 0, 5) === 'fetch')
                return $this->get(substr($name, 5), 'domain');
    }

    private function get($key, $switch)
    {
        switch ($switch):
            case 'get':
                return $this->getParam($key);
                break;
            case 'fetch':
                return $this->urlComponents[$key];
                break;
            default:
                return false;
                break;
        endswitch;
    }
    
    /**
     * Returns the domain name WITHOUT tld
     * 
     * @return string The domain name WITHOUT tld
     */
    
    public function getDomainName()
    {
        return $this->domainComponents['domain'];
    }
    
    /**
     * Return domains tld
     * 
     * @return string The domain tld
     */
    public function getTld()
    {
        return $this->domainComponents['tld'];
    }
    /**
     * Preforms a check against the request to determinate its type
     * 
     * @return string Textual representation of the request type or 'Uunknown'
     */
    public function getType()
    {
        switch($_SERVER['REQUEST_METHOD']):
        case self::REQ_GET:
            return strtoupper($_SERVER['REQUEST_METHOD']);
            break;
        case self::REQ_POST:
            return strtoupper($_SERVER['REQUEST_METHOD']);
            break;
        case self::REQ_PUT:
            return strtoupper($_SERVER['REQUEST_METHOD']);
            break;
        case self::REQ_DELETE:
            return strtoupper($_SERVER['REQUEST_METHOD']);
            break;
        case self::REQ_HEAD:
            return strtoupper($_SERVER['REQUEST_METHOD']);
            break;
        default:
            return 'Unknown';
            break;
        endswitch;
    }
    
    /**
     * Adding additional parameters to the request.
     * 
     * @param string $key key to associate with the $value
     * @param mixed $value A variable of any type to store against $key
     * @return \Forge\Router\Request
     */
    public function setParams($key, $value)
    {
        
        $this->params[$key] = $value;
        return $this;
    }
    
    
    /**
     * Preforms a search for the specified $key in the parameters list
     * 
     * @param string $key the key to search the values
     * @throws \InvalidArgumentException
     */
    public function getParam($key)
    {
        if (!array_key_exists($key, $this->params)) {
            throw new \InvalidArgumentException(
                    "The request parameter with key: '$key' is invalid.");
        }
            return $this->params[$key];
    }
    
    /**
     * Returns all parameters associated with the request as array
     * 
     * @return array Returns array with the parameters associated with the current request
     */
    public function getParams()
    {
        return $this->params;
    }
    
}