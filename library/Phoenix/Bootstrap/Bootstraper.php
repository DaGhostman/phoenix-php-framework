<?php

namespace Phoenix\Bootstrap;
use Phoenix\Storage\Registry;
use Phoenix\Router\Request;
use Phoenix\Router\Response;
use Phoenix\Core\HttpErrorsManager;
use Phoenix\Application\Exception\Runtime;


class Bootstraper
{
    final public function __construct() {
        $cfg = Registry::get('config', 'SystemCFG');
        
        if (array_key_exists('application.protectdir', $cfg)) {
            foreach ($cfg['application.protectdir'] as $dir) {
                if (fnmatch($dir, Request::getInstance()->getUri())) {
                    Response::getInstance()->sendStatusCode(403);
                    HttpErrorsManager::getInstance()->sendError(
                            403,
                            new Runtime(
                                    'Unauthorized attempt to access ' . 
                                    Request::getInstance()->getUri()
                                    )
                            );
                }
            }
        }
        
        if (array_key_exists('application.logstream.enable', $cfg)){
            if ((!in_array('log', stream_get_wrappers())) && 
                    ($cfg['application.logstream.enable'] == 1)) {
                stream_register_wrapper("log", "Phoenix\Core\Streams\LogStream");
            }
        }
        
        if (array_key_exists('application.forcessl', $cfg)) {
            switch ($cfg['application.forcessl']) {
                case 'soft':
                    Response::getInstance()
                        ->sendStatusCode(301, 
                                'https://'.$_SERVER['SERVER_NAME'] . 
                                $_SERVER['REQUEST_URI']);
                    break;
                
                case 'hard':
                    Response::getInstance()
                        ->sendStatusCode(301, 
                                $cfg['application.forcessl.hardurl']);
                    break;
            }
        }
        
        
        
        
    }
    
    final public function warmup()
    {
        
        if (version_compare(phpversion(), "5.3.0", "<"))
            trigger_error("Minimal PHP Version required is 5.3.x", E_USER_WARNING);
        
        ini_set('always_populate_raw_post_data', 1);
                ini_set('auto_detect_line_endings', TRUE);
    }
    
}