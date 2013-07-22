<?php

namespace Phoenix\View;

class Helper {
    
    public static function getHelper($name)
    {    
        $class = 'Phoenix\View\Helper\\'.$name;
        if (class_exists($class))
            return new $class;
        else 
            throw new \InvalidArgumentException('You have requested invalid helper'); 
    }
    
    
}

?>