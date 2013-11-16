<?php
namespace Phoenix\Mail;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Send {
    
    const ENC_PLAIN = 'plain';
    const ENC_HTML  = 'html';
    
    private $instance = null;
    protected $_requestSendNotification = false,
            $_requestReadNotification = true,
            $_sender = '',
            $_senderE = '',
            $_rcpt = '',
            $_rcptE = '',
            $_subject = '',
            $_message = '',
            $_bcc = array(),
            $_cc = array(),
            $_replyTo = '',
            $_replyToE = '',
            $_notify = '';
    
    private $server, $port, $host, $username, $password;
    
    public function __construct($username, $password, $server, $port = 25, $host = 'localhost')
    {
        $this->username = $username;
        $this->password = $password;
        $this->server = $server;
        $this->port = (int) $port;
        $this->host = $host;
        
    }
    
    public function message($message, $content = 'plain')
    {
        switch ($content):
            case self::ENC_PLAIN:
                $this->_message = htmlentities($message, ENT_QUOTES, 'UTF-8');
                break;
            case self::ENC_HTML:
                $this->_message = '<html><body>';
                $this->_message .= addslashes($message);
                $this->_message .= '</body></html>';
                break;
        endswitch;
        
        return $this;
    }
    
    public function receiveNotification($bool)
    {
        $this->_requestSendNotification = (bool) $bool;
        
        return $this;
    }
    
    public function readNotification($bool)
    {
        $this->_requestReadNotification = (bool) $bool;
        
        return $this;
    }
    
    public function sender($email ,$name = '', $organisation = '') {
        
        if ('' !== trim($name)):
            $this->_senderE = '"'.$name.'" <'.filter_var(
                    filter_var($email, FILTER_SANITIZE_EMAIL), 
                    FILTER_VALIDATE_EMAIL).
                    '>';
        else:
            $this->_senderE = '<'.filter_var(
                    filter_var($email, FILTER_SANITIZE_EMAIL), 
                    FILTER_VALIDATE_EMAIL).
                '>';
        endif;
        
        $this->_sender = filter_var(
                filter_var($email, FILTER_SANITIZE_EMAIL), 
                FILTER_VALIDATE_EMAIL);
        
        if (empty($this->_replyTo))
            $this->_replyTo = filter_var(
                    filter_var($email, FILTER_SANITIZE_EMAIL), 
                    FILTER_VALIDATE_EMAIL);
        
        $this->_organisation = $organisation;
        
        return $this;
    }
    
    public function to($email ,$name = '') {
        
        if ('' !== trim($name)):
            $this->_rcptE = 
                    '"'.$name.'" <'.
                    filter_var(
                            filter_var($email, FILTER_SANITIZE_EMAIL), 
                            FILTER_VALIDATE_EMAIL)
                    .'>';
        else:
            $this->_rcptE = '<'.filter_var(
                    filter_var($email, FILTER_SANITIZE_EMAIL), 
                    FILTER_VALIDATE_EMAIL)
                .'>';
        endif;
        
        $this->_rcpt = filter_var(
                filter_var($email, FILTER_SANITIZE_EMAIL), 
                FILTER_VALIDATE_EMAIL);
        
        
        return $this;
    }
    
    public function subject($message, $priority = 3)
    {
        $this->_subject = addslashes($message);
        $this->_priority = $priority;
        return $this;
    }
    
    public function replyTo($email, $name)
    {
        
        if ('' !== trim($name)):
            $this->_rplyToE = addslashes(
                    '\"'.$name.'\" <'.
                    filter_var(
                            filter_var($email, FILTER_SANITIZE_EMAIL), 
                            FILTER_VALIDATE_EMAIL)
                    .'>');
        else:
            $this->_rplyToE = '<'.filter_var(
                    filter_var($email, FILTER_SANITIZE_EMAIL), 
                    FILTER_VALIDATE_EMAIL)
                .'>';
        endif;
        
        
        $this->_replyTo = filter_var(
                filter_var($email, FILTER_SANITIZE_EMAIL), 
                FILTER_VALIDATE_EMAIL);
        
        return $this;
    }

    public function notify($email)
    {
        $this->_notify = filter_var(
                filter_var($email, FILTER_SANITIZE_EMAIL), 
                FILTER_VALIDATE_EMAIL);
        
        return $this;
    }
    
    
    public function bcc($email) {
        array_push($this->_bcc, $email);
        
        return $this;
    }
    
    public function multiBcc($emails)
    {
        $this->_bcc = array_merge($this->_bcc, $emails);
        
        return $this;
    }
    
    public function cc($email)
    {
        array_push($this->_cc, $email);
        
        return $this;
    }
    
    public function multiCc($emails)
    {
        $this->_cc = array_merge($this->_cc, $emails);
        
        return $this;
    }
    

    public function send()
    {
        $this->instance = fsockopen($this->server, $this->port);
        
        fputs($this->instance, 'EHLO '.$this->host.PHP_EOL);
        fputs($this->instance, 'AUTH LOGIN'.PHP_EOL);
        fputs($this->instance, base64_encode($this->username).PHP_EOL);
        fputs($this->instance, base64_encode($this->password).PHP_EOL);
        fputs($this->instance, "MAIL FROM: {$this->_sender}".PHP_EOL);
        fputs($this->instance, "RCPT TO: <{$this->_rcpt}>".PHP_EOL);
        fputs($this->instance, "DATA".PHP_EOL);
       // if ($this->_requestReadNotification === true) // if notification on message read is requested
           // fputs($this->instance, "Disposition-Notification-To: {$this->_senderE}".PHP_EOL);
       // if ($this->_requestSendNotification === true) // if notification on message delivery is requested
           // fputs($this->instance, "Return-Receipt-To: {$this->_senderE}".PHP_EOL);
        fputs($this->instance, "Date: ".date("r").PHP_EOL);
        fputs($this->instance, "From: {$this->_replyTo}".PHP_EOL);
        if ('' != $this->_organisation)
            fputs($this->instance, "Organisation: {$this->_organisation}".PHP_EOL);
        fputs($this->instance, "X-UA: Phoenix PHP Framework/1.x".PHP_EOL);
        fputs($this->instance, "X-Software: PrimeManager(tm)".PHP_EOL);
        fputs($this->instance, "MIME-Version: 1.0".PHP_EOL);
        fputs($this->instance, "Sender: {$this->_sender}".PHP_EOL);
        fputs($this->instance, "Reply-to: {$this->_replyTo}".PHP_EOL);
        fputs($this->instance, "To: {$this->_rcptE}".PHP_EOL);
        fputs($this->instance, "Subject: $this->_subject".PHP_EOL.PHP_EOL);
        fputs($this->instance, $this->_message.PHP_EOL.PHP_EOL);
        
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
