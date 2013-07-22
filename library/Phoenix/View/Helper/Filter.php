<?php

namespace Phoenix\View\Helper;

class Filter {
    
    const ESCAPE_EMAIL = FILTER_SANITIZE_EMAIL;
    const ESCAPE_STRING = FILTER_SANITIZE_STRING;
    const ESCAPE_ENTITIES = FILTER_SANITIZE_FULL_SPECIAL_CHARS;
    const ESCAPE_HTML = FILTER_SANITIZE_SPECIAL_CHARS;
    const ESCAPE_INT  = FILTER_SANITIZE_NUMBER_FLOAT;
    const ESCAPE_QUOTES = FILTER_SANITIZE_MAGIC_QUOTES;
    const ESCAPE_URL = FILTER_SANITIZE_URL;
    
    
    /**
     * Filters a value with the specified filter(s)
     * 
     * Please not that this function utilizes php's built in
     * function <strong>filter_var()</strong>
     * 
     * 
     * @param string $var
     * @param const|array $type one or array of Filter::ESCAPE_* constants
     * @return mixed the filtered value
     */
    public function escape($var, $type)
    {
        if (is_array($type)):
            return $this->escapeArray($var, $type);
        else:
            return filter_var($var, $type);
        endif;
    }
    
    private function escapeArray($var, $type)
    {
        
        foreach($type as $filter):
            $var = filter_var($var, $filter);
        endforeach;
        
        return $var;
    }
    
}

?>