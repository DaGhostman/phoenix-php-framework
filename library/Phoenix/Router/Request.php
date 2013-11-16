<?php

namespace Phoenix\Router;

class Request {
    
    const REQ_GET       = 'GET';
    const REQ_POST      = 'POST';
    const REQ_PUT       = 'PUT';
    const REQ_DELETE    = 'DELETE';
    const REQ_HEAD      = 'HEAD';
    
    protected $uri,
            $params = array(), 
            $serverName,
            $domainComponents,
            $urlComponents,
            $__route = null;
    
    private static $instance = null;
    
    /**
     * 
     * Creates the instance of  the current request object
     * for web application the <strong>$_SERVER['REQUEST_URI']</strong>
     * should be fine. For CLI applications should be set as to 
     * match the patterns used in the routes.
     * 
     * @param string $uri The current request address
     * @param array $params a
     */
    public static function getInstance($uri = null)
    {
        
        if (!self::$instance)  {
            self::$instance = new Request($uri);
        }
    
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
    private function __construct($uri = '/')
    {
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
    
    public function __call($name, $args = array()) {
        if (substr(strtolower($name), 0, 3) === 'get')
                return $this->get(substr($name, 3), 'param');
        
        if (substr(strtolower($name), 0, 5) === 'fetch')
                return $this->get(substr($name, 5), 'domain');
    }

    private function get($key, $switch)
    {
        switch ($switch):
            case 'param':
                return $this->getParam($key);
                break;
            case 'domain':
                return $this->urlComponents[$key];
                break;
            default:
                return false;
                break;
        endswitch;
    }

    /**
     * Preforms a check against the request to determinate its type
     * 
     * @return string Textual representation of the request type or 'Uunknown'
     */
    public function getType()
    {
        if (array_key_exists('REQUEST_METHOD', $_SERVER)) {
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
            return 'UNKNOWN';
            break;
        endswitch;
        }
        
        return 'CLI';
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
    
    public function setRoute($route)
    {
        $this->__route = $route;
    }
    
    public function getRoute()
    {
        return $this->__route;
    }
    
}