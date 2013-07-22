<?php

/**
 * 
 * @author Dimitar Dimitrov <daghostman.dd@gmail.com>
 * @link http://web-forge.org/
 * @copyright (c) 2013, Dimitar Dimitrov
 * @license http://creativecommons.org/licenses/by-sa/3.0/ Attribution-ShareAlike 3.0 Unported
 * 
 */
namespace Phoenix\Plugin;

abstract class IPlugin {

    /**
     * @abstract
     * @access public
     * This function is used only for the purpose of making the plugin
     * to connect itself to the slot it needs
     * @example 
     * class Plugin {
     *   public function __() {
     *     Manager::bind(SIGNAL_ID, function(){print "2";});
     *     // So now when the SIGNAL_ID is send to the manager
     *     // this plugin will print 2. As advice should be taken
     *     // to always make a function that invokes the Plugin
     *     // Methods either in the specified order or to invoke
     *     // the triggering method.
     *   }
     * }
     * @link https://code.google.com/p/rebirth-php-framework/ documentation 
     * on usage of plugins
     */
    abstract public static function __();

}

?>
