<?php

namespace Phoenix\View\Helper;

use Phoenix\Router\Enroute;

use Phoenix\Router\Route;

class Url {
    
    private $router = null;
    
    /**
     * Creates a route for the link
     * 
     * @param string $URI the RESTful URI to display and to match the request with
     * @param array $route Asoociative array containing keys: <strong>module</string>, <strong>controller</strong> and <strong>action</strong>
     * @param bool $forceSSL True to force the URLs to use HTTPS false to use current protocol
     * @return string Full link to be displayed
     */
    public function build($URI = '/', array $route = array(), $forceSSL = false)
    {
        
        
        $route = new Route($URI, $route);
        
        Enroute::getInstance($route)->addRoute($route);
        
        
        switch($forceSSL):
        case false:
            return '//' . $_SERVER['HTTP_HOST'].$URI;
            break;
        case true:
            return 'https://' . $_SERVER['HTTP_HOST'].$URI;
            break;
        endswitch;
    }
    
}

?>