<?php

namespace Phoenix\View\Helper;

class Filter {
    
    const HTML = 'html_special',
            ENTITIES = 'html_entities',
            SHELL = 'shell_escape',
            MB_ENCODING = 'mb_covert';
    
    /**
     * 
     * @param string $input The input string to sanitize
     * @param mixed $sanitize array or a single const to strip the input
     * 
     * @return string Returns a sanitized version string
     */
    public static function sanitize($input, $sanitize = self::HTML){}
    
    
}

?>