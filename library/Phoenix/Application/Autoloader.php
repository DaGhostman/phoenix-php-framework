<?php

namespace Phoenix\Application;

class Autoloader {

    private $_fileExtension = '.php';
    private $_namespace = null;
    private $_includePath = null;
    private $_namespaceSeparator = '\\';

    /**
     * Creates an instance of the Main autoloader
     * 
     * @param string $ns namesapce
     * @param string $includePath include path to use
     * @return \Forge\Application\Autoloader
     */
    public function __construct($ns = NULL, $includePath = NULL) {
        $this->_namespace = $ns;
        $this->_includePath = $includePath;

        defined('REAL_PATH') ||
                define('REAL_PATH', realpath($ns . '../'));

        return $this;
    }

    /**
     * Sets the namespace separator
     * 
     * @param unknown $sep the namespace separator
     * @return \Forge\Application\Autoloader
     */
    public function setSeparator($sep) {
        $this->_namespaceSeparator = $sep;

        return $this;
    }

    /**
     * Return the namespace separator
     * @return string namespace separator
     */
    public function getSeparator() {
        return $this->_namespaceSeparator;
    }

    /**
     * Sets the include path to use when autoloading
     * 
     * @param string $path directory path to use for autoloading
     * @return \Forge\Application\Autoloader
     */
    public function setIncludePath($path) {

        $this->_includePath = $path;

        return $this;
    }

    /**
     * Returns the used include path
     * 
     * @return string
     */
    public function getIncludePath() {
        return $this->_includePath;
    }

    /**
     * Sets the file extension for autoloading.
     * Defaults to <strong>'.php'</strong>
     * 
     * @param string $exp the file extension to use (including the (dot))
     * @example $autoloaderInstance->setExtension('<strong>.</strong>php');
     * @return \Forge\Application\Autoloader
     */
    public function setExtension($ext) {
        $this->_fileExtension = $ext;

        return $this;
    }

    /**
     * Returns the file extensino used
     * 
     * @return string
     */
    public function getExtencion() {
        return $this->_fileExtension;
    }

    /**
     * Registers the Autoloader::autoload(); as autoloader
     * should be called allways before using any class
     * @return \Forge\Application\Autoloader
     */
    public function register() {
        return spl_autoload_register(array($this, 'autoload'), true, false);
    }

    /**
     * Removes the Autoloader::autoload(); from the autoloaders stack.
     * <strong>Note:</strong> users should know that, invloking this method will
     * cause loss of autoloading functionality and the loading should be done either
     * manually or with another autoloader.
     * 
     * @return \Forge\Application\Autoloader
     */
    public function unregister() {
        spl_autoload_unregister(array($this, 'autoload'));

        return $this;
    }

    /**
     * The autoloading method, that does the actual autoloading.
     * <strong>Note:</strong> This function should not be called manually,
     * instead use <strong>$autoloaderInstance->register();</strong>
     * 
     * @param string $className
     */
    public function autoload($className) {
        $fileName = '';
        $namespace = '';
        if (null === $this->_namespace ||
                $this->_namespace . $this->_namespaceSeparator ===
                substr($className, 0, strlen($this->_namespace . $this->_namespaceSeparator))) {
            if (false !== ($lastNsPos = strripos($className, $this->_namespaceSeparator))) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName = str_replace($this->_namespaceSeparator, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }
            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) .
                    $this->_fileExtension;
            
            if (is_readable(($this->_includePath ?
                                    $this->_includePath . DIRECTORY_SEPARATOR :
                                    '') . $fileName)) {
                require_once ($this->_includePath ?
                                $this->_includePath . DIRECTORY_SEPARATOR :
                                '') . $fileName;
                
            }
            
        }
    }

}