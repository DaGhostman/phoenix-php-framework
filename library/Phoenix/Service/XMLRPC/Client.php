<?php
namespace Phoenix\Service\XMLRPC;

/**
 *
 * @author daghostman
 *        
 */
class Client
{

    protected $url = null,
    $_headers = array(
        'Content-Type: text/plain'
    ), $data;
    
    
    function __construct($url)
    {
    	if ( filter_var($url, FILTER_VALIDATE_URL))
    	{
    	    $this->url = $url;
    	}
    }

    public function init()
    {
        $this->curl = curl_init($this->url);
        
        return $this;
    }
    
    public function addHeader($header)
    {
        array_push($this->_headers, $header);
        
        return $this;
    }
    
    public function setData($data)
    {
        $this->_data = $data;
        $this->addHeader('Content-Length: '.strlen($data));
        
        return $this;
    }
    
    public function send()
    {
        $this->addHeader('\n\r');
        curl_setopt($this->curl, CURLOPT_URL, $this->url);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->_headers );
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->data);
        
        $result = curl_exec($this->curl);
        curl_close($this->curl);
        
        return xmlrpc_decode($result);
    }
    
}

?>