<?php
namespace Phoenix\Mail;

//use Phoenix\Core\HttpErrorsManager;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Send {
    
    private $instance = null;
    
    
    public function __construct($username, $password, $server, $port = 25, $host = 'localhost')
    {
        
        $this->instance = fsockopen($server, $port);
        
        fputs($this->instance, 'EHLO '.$host.PHP_EOL);
        fputs($this->instance, 'AUTH LOGIN'.PHP_EOL);
        fputs($this->instance, base64_encode($username).PHP_EOL);
        fputs($this->instance, base64_encode($password).PHP_EOL);
    }
    
    public function message($sender, $receiver, $subject, $message)
    {
        fputs($this->instance, "MAIL FROM: <$sender>".PHP_EOL);
        fputs($this->instance, "RCPT TO: <$receiver>".PHP_EOL);
        fputs($this->instance, "DATA".PHP_EOL);
        fputs($this->instance, "To: $receiver".PHP_EOL);
        fputs($this->instance, "Subject: $subject".PHP_EOL.PHP_EOL);
        fputs($this->instance, $message.PHP_EOL.PHP_EOL);
        
        
        return $this;
    }
    
    public function send()
    {
        fputs($this->instance, ".".PHP_EOL);
    }
    
    public function __destruct()
    {
        fputs($this->instance, "QUIT".PHP_EOL);
        fclose($this->instance);
        $this->instance = null;
    }
}

?>
