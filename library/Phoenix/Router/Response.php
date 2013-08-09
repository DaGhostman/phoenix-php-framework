<?php

namespace Phoenix\Router;
use Phoenix\Core\SignalSlot\Manager;
use Phoenix\Core\SignalSlot\Signals;


class Response {
    
    /**
     * @type const
     * Definition of the HTTP/1.0 protocol
     */
    const V10 = 'HTTP/1.0';
    
    /**
     * @type const
     * Definition of the HTTP/1.1 protocol
     */
    const V11 = 'HTTP/1.1';
    
    /**
     * @type const
     * Definition of the "<strong>200 OK</strong>" HTTP response code
     */
    const HTTP_200 = 200;
    
    /**
     * @type const
     * Definition of the "<strong>301 Moved Permanently</strong>" HTTP response code
     */
    const HTTP_301 = 301;
    
    /**
     * @type const
     * Definition of the "<strong>302 Found</strong>" HTTP response code
     */
    const HTTP_302 = 302;
    
    /**
     * @type const
     * Definition of the "<strong>401 Unauthorized</strong>" HTTP response code
     */
    const HTTP_401 = 401;
    
    /**
     * @type const
     * Definition of the "<strong>403 Forbidden</strong>" HTTP response code
     */
    const HTTP_403 = 403;
    
    /**
     * @type const
     * Definition of the "<strong>403 Forbidden</strong>" HTTP response code
     */
    const HTTP_404 = 404;
    
    /**
     * @type const
     * Definition of the "<strong>500  Internal Server Error</strong>" HTTP response code
     */
    const HTTP_500 = 500;
    
    /**
     * @type const
     * Definition of the "<strong>503 Service Temporarily Unavailable</strong>" HTTP response code
     */
    const HTTP_503 = 503;
    
    protected $version;
    protected $headers = array();
    
    private static $instance = null;
    
    public static function getInstance($version = self::V11)
    {
        if (!self::$instance) self::$instance = new Response($version);
        
        return self::$instance;
    }
    
    private function __construct($version = self::V11)
    {
        $this->version = $version;
    }
    
    public function getVersion()
    {
        return $this->version;
    }
    
    public function addHeader($header)
    {
        $this->headers[] = $header;
        
        return $this;
    }
    
    public function sendStatusCode($code, $argc = null)
    {
        switch ($code):
            case self::HTTP_200:
                header($this->getVersion() . ' 200 OK', true, 200);
                break;
            case self::HTTP_301:
                header($this->getVersion() . ' 301 Moved Permanently', true, 301);
                header ('Location: '.$argc, true);
                break;
            case self::HTTP_302:
                header ('Location: '.$argc, true);
                break;
            case self::HTTP_401:
                header($this->getVersion() . ' 401 Unauthorized', true, 401);
                break;
            case self::HTTP_403:
                header($this->getVersion() . ' 403 Forbidden', true, 403);
                break;
            case self::HTTP_404:
                header($this->getVersion() . ' 404 Not Found', true, 404);
                break;
            case self::HTTP_500:
                header($this->getVersion() . ' 500 Internal Server Error', true, 500);
            case self::HTTP_503:
                header($this->getVersion() . ' 503 Service Temporarily Unavailable', true, 503);
                header('Status: 503 Service Temporarily Unavailable', true);
                header('Retry-After: 300', true);
                break;
        endswitch;
    }
    
    public function addHeaders(array $headers)
    {
        foreach ($headers as $header) $this->addHeader($header);
        
        return $this;
    }
    
    public function getHeaders()
    {
        return $this->headers;
    }
    
    public function send($code = null)
    {
        if(!headers_sent()):
            Manager::getInstance()->emit(Signals::SIGNAL_RESPONSE);
            foreach ($this->headers as $header):
                header($this->getVersion()." ".$header, true, $code);
            endforeach;
        endif;
    }
    
}