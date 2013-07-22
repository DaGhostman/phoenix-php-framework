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
     * Definition of the "<strong>401 Unauthorized</strong>" HTTP response code
     */
    const HTTP_401 = 1;
    
    /**
     * @type const
     * Definition of the "<strong>403 Forbidden</strong>" HTTP response code
     */
    const HTTP_403 = 2;
    
    /**
     * @type const
     * Definition of the "<strong>403 Forbidden</strong>" HTTP response code
     */
    const HTTP_404 = 3;
    
    /**
     * @type const
     * Definition of the "<strong>500  Internal Server Error</strong>" HTTP response code
     */
    const HTTP_500 = 4;
    
    /**
     * @type const
     * Definition of the "<strong>503 Service Temporarily Unavailable</strong>" HTTP response code
     */
    const HTTP_503 = 5;
    
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
        switch ($header):
            case self::HTTP_401:
                $this->headers[] = '401 Unauthorized';
                break;
            case self::HTTP_403:
                $this->headers[] = '403 Forbidden';
                break;
            case self::HTTP_404:
                $this->headers[] = '404 Not Found';
                break;
            case self::HTTP_500:
                $this->headers[] = '500 Internal Server Error';
                break;
            case self::HTTP_503:
                $this->headers[] = '503 Service Temprarily Unavailable';
                $this->headers[] = 'Status: 503 Service Temporarily Unavailable';
                $this->headers[] = 'Retry-After: 60';
                break;
            default:
                $this->headers[] = $header;
                break;
        endswitch;
        
        return $this;
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
    
    public function send()
    {
        if(!headers_sent()):
            Manager::getInstance()->emit(Signals::SIGNAL_RESPONSE);
            foreach ($this->headers as $header):
                header($this->getVersion()." ".$header, true);
            endforeach;
        endif;
    }
    
}