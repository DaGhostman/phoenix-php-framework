<?php
namespace Phoenix\View\Helper;

class Head
{
    private $doctype = '';
    public function doctype($id)
    {
        switch($id):
        case 'html5':
            $this->doctype = '<!DOCTYPE html>';
            break;
        case 'html4strict':
            $this->doctype = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" 
                    "http://www.w3.org/TR/html4/strict.dtd">';
            break;
        case 'html4loose':
            $this->doctype = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
                    "http://www.w3.org/TR/html4/loose.dtd">';
            break;
        case 'html4frameset':
            $this->doctype = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" 
                    "http://www.w3.org/TR/html4/frameset.dtd">';
            break;
        case 'xhtml1strict':
            $this->doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
                    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
            break;
        case 'xhtml1loose':
            $this->doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
                    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
            break;
        case 'xhtml1frameset':
            $this->doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" 
                    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
            break;
        case 'xhtml11':
            $this->doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
                    "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
            break;
        endswitch;
        
        return $this->doctype;
    }
    
    
    public function stylesheet($filepath)
    {
        if (!preg_match('/http|s\:\/\//i', $filepath))
            return '<link rel="stylesheet" type="text/css" href="http://'.$_SERVER['HTTP_HOST'].'/'.$filepath.'" />';
        else 
            return '<link rel="stylesheet" type="text/css" href="'.$filepath.'" />';
    }
    
    public function javascript($filepath)
    {
        if (!preg_match('/http|s\:\/\//i', $filepath))
            return '<script type="text/javascript" href="http://'.$_SERVER['HTTP_HOST'].'/'.$filepath.'" ></script>';
        else
            return '<script type="text/javascript" href="'.$filepath.'" ></script>';
    }
    
    public function setTitle($title)
    {
        return '<title>'.$title.'</title>';
    }
}