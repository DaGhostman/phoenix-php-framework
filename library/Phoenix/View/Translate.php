<?php

/**
 * 
 * @author Dimitar Dimitrov <daghostman.dd@gmail.com>
 * @link http://web-forge.org/
 * @copyright (c) 2013, Dimitar Dimitrov
 * @license  GNU GPLv3
 * Phoenix PHP Framework - Another MVC framework
 *   Copyright (C) 2013  Dimitar Dimitrov
 *
 * 
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

namespace Phoenix\View;
use Phoenix\View\Translate;
use Phoenix\Application\Configurator;

class Translate {
    
    private $dir = '/application/data/language/';
    protected $lang = 'en';
    protected $xml = null;

    protected static $_instance = null;
    
    /**
     * Method to override the language supplied by the Accept-Language.
     * The usage of it could be usefull when internationalization and
     * targeted SEO should be achieved.
     * 
     * @access public
     * @param string $lang
     * @return self
     */
    public function setLanguage($lang) {
    	$this->lang = $lang;
    	
    	return $this;
    }
    
    public function __construct($config, $language = null) {
        
        $this->lang = $language ? $language : $this->getPrefferedLanguage($config);
    }
    
    /**
     * 
     * The true translation method, which translates the code string
     * in the language detected or the one defined by the user.
     * 
     * @access public
     * @param string $string the string which should be translated.
     * @return string Returns the translation of the code string or
     * the code string if the translation is not found.
     */
    public function translate($string)
    {
        if (empty($this->xml)) {
            $this->parse();
        }
        
            if($this->xml != null) {
                $seg = $this->xml->xpath(
                        '//tu[@id="' . $string . '"]/tuv[@lang="' . $this->lang . '"]'
                        );
            
                if (($seg == false) || (empty($seg) || (isset($seg[0])))) {
                	$seg = $this->xml->xpath(
                        '//entry[@id="' . $string . '"][@lang="' . $this->lang . '"]'
                        );
                	
                	$result = $seg[0];
                } else {
                    $result = $seg[0]->seg[0];
                }
            } else {
                return $string;
            }
	
            
        return $result ? $result : $string;
    }
    
    /**
     * 
     * Parses the XML document which contains the translation definitions.
     * The file name should be the language code determinated by the Accept-Language
     * or by the manual defined language.
     * 
     * @access protected
     */
    protected function parse()
    {
        $filename = REAL_PATH . $this->dir . 
                                $this->lang . '.xml';
        
        if (is_file($filename) && is_readable($filename))
            $this->xml = new \SimpleXMLElement($filename, NULL, TRUE);
        
        
        return; 
    }

    
    /**
     * 
     * The method which is used to parse the 'Accept-Language' header
     * and determinate the user language based on the 
     */
    public function getPrefferedLanguage($cfg)
    {
        
        $websiteLanguages = array();
        foreach($cfg['language-supported'] as $value)
            $websiteLanguages[] = strtolower($value);
        
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
        {
            $langParse = null;
            
            preg_match_all(
                    '/([a-z]{1,8})' . 
                    '(-[a-z]{1,8})*\s*' . 
                    '(;\s*q\s*=\s*((1(\.0{0,3}))|(0(\.[0-9]{0,3}))))?/i',
                    $_SERVER['HTTP_ACCEPT_LANGUAGE'],
                    $langParse);
            
            
            $langs = $langParse[1];
            $quals = $langParse[4];
            
            $numLanguages = count($langs);
            
            $langArr = array();
            for ($num = 0; $num < $numLanguages; $num++)
            {
                $newLang = strtolower($langs[$num]);
                $newQual = isset($quals[$num]) ?
                        (empty($quals[$num]) ? 1.0 : floatval($quals[$num])) : 0.0;
                
                
                $langArr[$newLang] = (isset($langArr[$newLang])) ?
                        max($langArr[$newLang], $newQual) : $newQual;
            }
            
            arsort($langArr, SORT_NUMERIC);
            
            $acceptedLanguages = array_keys($langArr);
            
            foreach ($acceptedLanguages as $preferredLanguage)
            {
                if (in_array(strtolower($preferredLanguage), $websiteLanguages))
                {
                    
                    return $preferredLanguage;
                    
                }
            }
        } else {
            return $cfg['language-default'];
        }
        
        
    }
    
    public function getCurrentLanguage()
    {
        return $this->lang;
    }
}


?>
