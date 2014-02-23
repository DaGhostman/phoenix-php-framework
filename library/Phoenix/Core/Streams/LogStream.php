<?php
namespace Phoenix\Core\Streams;

class LogStream {
    private $position, $varname, $filename;
    
    public function stream_open($path, $mode, $options, &$opend_path)
    {
        if (!is_dir(REAL_PATH . '/application/data/logs'))
            mkdir(REAL_PATH . '/application/data/logs', 0755, true);
        $url = str_replace('log://', '', $path);
        $this->varname = fopen(REAL_PATH . '/application/data/logs/'.$url, $mode);
        $this->filename = REAL_PATH . '/application/data/logs/'.$url;
        $this->position = 0;
        
        return true;
    }
    
    public function stream_read($count)
    {
        flock($this->varname, LOCK_EX | LOCK_NB);
        $p = &$this->position;
        $ret = substr($GLOBALS[$this->varname], $p, $count);
        $p += strlen($ret);
        flock($this->varname, LOCK_UN);
        
        return $ret;
    }
    
    public function stream_write($data)
    {
        return fwrite($this->varname, $data);
    }
    
    public function stream_tell()
    {
        return $this->position;
    }
    
    public function stream_eof()
    {
        flock($this->varname, LOCK_EX | LOCK_NB);
        $r =  $this->position >= strlen(stream_get_contents($this->varname));
        flock($this->varname, LOCK_UN);
        
        return $r;
    }
    
    public function stream_stat()
    {
        return fstat($this->varname);
    }
    
    public function stream_seek($offset, $whence)
    {
        flock($this->varname, LOCK_EX | LOCK_NB);
        $r = stream_get_contents($this->varname);
        $l = strlen($r);
        $p = &$this->position;
        
        switch($whence):
            case SEEK_SET: $newPos = $offset; break;
            case SEEK_CUR: $newPos = $p + $offset; break;
            case SEEK_END: $newPos = $l + $offset; break;    
            default: return false;
        endswitch;
            
        $ret = ($newPos >= 0 && $newPos <= $l);
        if ($ret) $p=$newPos;
        flock($this->varname, LOCK_UN);
        return $ret;
    }
}


?>