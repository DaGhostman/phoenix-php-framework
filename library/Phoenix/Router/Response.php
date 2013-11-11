<?php

namespace Phoenix\Router;


class Response {
    
    /**
     * Definition of the HTTP/1.0 protocol
     * @type const
     */
    const V10 = 'HTTP/1.0';
    
    /**
     * Definition of the HTTP/1.1 protocol
     * @type const
     */
    const V11 = 'HTTP/1.1';
    
    /**
     * Definition of the "<strong>200 OK</strong>" HTTP response code
     * @type const
     */
    const HTTP_200 = 200;
    
    /**
     * Definition of the "<strong>201 Created</strong>" HTTP response code
     * @type const
     */
    const HTTP_201 = 201;
    
    /**
     * Definition of the "<strong>202 Accepted</strong>" HTTP response code
     * @type const
     */
    const HTTP_202 = 202;
    
    /**
     * Definition of the "<strong>203 Non-Authorative Information</strong>" HTTP response code
     * @type const
     */
    const HTTP_203 = 203;
    
    /**
     * Definition of the "<strong>204 No Content</strong>" HTTP response code
     * @type const
     */
    const HTTP_204 = 204;
    
    /**
     * Definition of the "<strong>205 Reset Content</strong>" HTTP response code
     * @type const
     */
    const HTTP_205 = 205;
    
    /**
     * Definition of the "<strong>206 Partial Content</strong>" HTTP response code
     * @type const
     */
    const HTTP_206 = 206;
    
    /**
     * Definition of the "<strong>301 Moved Permanently</strong>" HTTP response code
     * @type const
     */
    const HTTP_301 = 301;
    
    /**
     * Definition of the "<strong>302 Found</strong>" HTTP response code
     * @type const
     */
    const HTTP_302 = 302;
    
    /**
     * Definition of the "<strong>400 Bad Request</strong>" HTTP response code
     * @type const
     */
    const HTTP_400 = 400;
    
    /**
     * Definition of the "<strong>401 Unauthorized</strong>" HTTP response code
     * @type const
     */
    const HTTP_401 = 401;
    
    /**
     * Definition of the "<strong>402 Payment Required</strong>" HTTP response code
     * @type const
     */
    const HTTP_402 = 402;
    
    /**
     * Definition of the "<strong>403 Forbidden</strong>" HTTP response code
     * @type const
     */
    const HTTP_403 = 403;
    
    /**
     * Definition of the "<strong>404 Not Found</strong>" HTTP response code
     * @type const
     */
    const HTTP_404 = 404;
    
    /**
     * Definition of the "<strong>405 Method Not Allowed</strong>" HTTP response code
     * @type const
     */
    const HTTP_405 = 405;
    
    /**
     * Definition of the "<strong>406 Not Acceptable</strong>" HTTP response code
     * @type const
     */
    const HTTP_406 = 406;
    
    /**
     * Definition of the "<strong>407 Proxy Authentication Required</strong>" HTTP response code
     * @type const
     */
    const HTTP_407 = 407;
    
    /**
     * Definition of the "<strong>408 Request Timeout</strong>" HTTP response code
     * @type const
     */
    const HTTP_408 = 408;
    
    /**
     * Definition of the "<strong>409 Conflict</strong>" HTTP response code
     * @type const
     */
    const HTTP_409 = 409;
    
    /**
     * Definition of the "<strong>410 Gone</strong>" HTTP response code
     * @type const
     */
    const HTTP_410 = 410;
    
    /**
     * Definition of the "<strong>411 Length Required</strong>" HTTP response code
     * @type const
     */
    const HTTP_411 = 411;
    
    /**
     * Definition of the "<strong>412 Precondition Failed</strong>" HTTP response code
     * @type const
     */
    const HTTP_412 = 412;
    
    /**
     * Definition of the "<strong>413 Request Entity Too Large</strong>" HTTP response code
     * @type const
     */
    const HTTP_413 = 413;
    
    /**
     * Definition of the "<strong>414 Request-URI Too Long</strong>" HTTP response code
     * @type const
     */
    const HTTP_414 = 414;
    
    /**
     * Definition of the "<strong>415 Unsupported Media Type</strong>" HTTP response code
     * @type const
     */
    const HTTP_415 = 415;
    
    /**
     * Definition of the "<strong>416 Reqest Range Not Satisfiable</strong>" HTTP response code
     * @type const
     */
    const HTTP_416 = 416;
    
    /**
     * Definition of the "<strong>417 Expectation Failed</strong>" HTTP response code
     * @type const
     */
    const HTTP_417 = 417;
    
    /**
     * Definition of the "<strong>500  Internal Server Error</strong>" HTTP response code
     * @type const
     */
    const HTTP_500 = 500;
    
    /**
     * Definition of the "<strong>501 Not Implemented</strong>" HTTP response code
     * @type const
     */
    const HTTP_501 = 501;
    
    /**
     * Definition of the "<strong>502 Bad Gateway</strong>" HTTP response code
     * @type const
     */
    const HTTP_502 = 502;
    
    /**
     * Definition of the "<strong>503 Service Temporarily Unavailable</strong>" HTTP response code
     * @type const
     */
    const HTTP_503 = 503;
    
    /**
     * Definition of the "<strong>504  Gateway Timeout</strong>" HTTP response code
     * @type const
     */
    const HTTP_504 = 504;
    
    /**
     * Definition of the "<strong>505 HTTP Version Not Supported</strong>" HTTP response code
     * @type const
     */
    const HTTP_505 = 505;
    protected $version;
    protected $headers = array();
    
    private static $instance = null;
    
    public static function &getInstance($version = self::V11)
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
            case self::HTTP_201:
            	header($this->getVersion() . ' 201 Created', true, 201);
            	break;
            case self::HTTP_202:
            	header($this->getVersion() . ' 202 Accepted', true, 202);
            	break;
            case self::HTTP_203;
            	header($this->getVersion() . ' 203 Non-Authorative Information', true, 203);
            	break;
            case self::HTTP_204:
            	header($this->getVersion() . ' 204 No Content', true, 204);
            	break;
            case self::HTTP_205:
            	header($this->getVersion() . ' 205 Reset Content', true, 205);
            	break;
            case self::HTTP_206:
            	header($this->getVersion() . ' 206 Partial Content', true, 206);
            	break;
            case self::HTTP_301:
                header($this->getVersion() . ' 301 Moved Permanently', true, 301);
                header ('Location: '.$argc, true, 301);
                break;
            case self::HTTP_302:
                header ('Location: '.$argc, true, 302);
                break;
            case self::HTTP_401:
                header($this->getVersion() . ' 401 Unauthorized', true, 401);
                break;
            case self::HTTP_402:
                header($this->getVersion() . ' 402 Unauthorized', true, 402);
                break;
            case self::HTTP_403:
                header($this->getVersion() . ' 403 Forbidden', true, 403);
                break;
            case self::HTTP_404:
                header($this->getVersion() . ' 404 Not Found', true, 404);
                break;
            case self::HTTP_405:
                header($this->getVersion() . ' 405 Method Not Allowed', true, 405);
                break;
            case self::HTTP_406:
                header($this->getVersion() . ' 406 Not Acceptable', true, 406);
                break;
            case self::HTTP_407:
                header($this->getVersion() . ' 407 Proxy Authentication Required', true, 407);
                break;
            case self::HTTP_408:
                header($this->getVersion() . ' 408 Request Timeout', true, 408);
                break;
            case self::HTTP_409:
                header($this->getVersion() . ' 409 Conflict', true, 409);
                break;
            case self::HTTP_410:
                header($this->getVersion() . ' 410 Gone', true, 410);
                break;
            case self::HTTP_411:
                header($this->getVersion() . ' 411 Length Required', true, 411);
                break;
            case self::HTTP_412:
                header($this->getVersion() . ' 412 Precondition Required', true, 412);
                break;
            case self::HTTP_413:
                header($this->getVersion() . ' 413 Request Entity Too Large', true, 413);
                break;
            case self::HTTP_414:
                header($this->getVersion() . ' 414 Request-URI Too Long', true, 414);
                break;
            case self::HTTP_415:
                header($this->getVersion() . ' 415 Gone', true, 415);
                break;
            case self::HTTP_416:
                header($this->getVersion() . ' 416 Request Range Satisfied', true, 416);
                break;
            case self::HTTP_417:
                header($this->getVersion() . ' 417 Expectation Field', true, 417);
                break;
            case self::HTTP_500:
                header($this->getVersion() . ' 500 Internal Server Error', true, 500);
                break;
            case self::HTTP_501:
                header($this->getVersion() . ' 501 Not implemented', true, 501);
                break;
            case self::HTTP_502:
                header($this->getVersion() . ' 502 Bad Gateway', true, 502);
                break;
            case self::HTTP_503:
                header($this->getVersion() . ' 503 Service Temporarily Unavailable', true, 503);
                header('Status: 503 Service Temporarily Unavailable', true);
                header('Retry-After: 300', true);
                break;
            case self::HTTP_504:
                header($this->getVersion() . ' 504 Gateway Timeout', true, 504);
                break;
            case self::HTTP_505:
                header($this->getVersion() . ' 505 HTTP Version Not Supported', true, 505);
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
    
    public function send()
    {
        if(!headers_sent()):
            foreach ($this->headers as $header):
                header($header, true);
            endforeach;
        endif;
    }
    
}