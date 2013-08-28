<?php

namespace Phoenix\Form\Security;

class ImagickCaptcha {
    
    /**
     *  
     * @var string $save_path The path to use for storing the images
     */
    private $save_path = null,
    /**
     * @var The default time to live is 360 seconds
     */
            $ttl = 360,
            
    /**
     * @var the list of options used for configuration
     */
            $options = array();
    
    
    protected $font = '';
    protected $font_size = 12;
    protected $font_color = '#000000';
                
    protected $drawer = null;
    protected $magick = null;
    
    /**
     * @access public
     * @method __construct creates the CaptchaImage object
     * @param $savePath A string representation of the path the the images will be saved.
     * @param $ttl integer The time in seconds that the created captcha image will be valid
     * @param $options array An array of options that will be used to configure the image generation process
     * @throws IMagickExceptions
     */
    public function __construct($savePath, $ttl = 360, array $options = array())
    {
        $this->save_path = $savePath ? $savePath : $this->save_path;
        $this->ttl = $ttl ? $ttl : $this->ttl;
        $this->options = $options ? $options : $options;
        
        $this->magick = new \Imagick();
        $this->drawer = new \ImagickDraw();
        
        return $this;
    }
    
    
    /**
     * @access public
     * @method setFont configures the font to use for the captcha image
     * @param string $path The path to the font
     * @param integer $size The font size to use, defaults to 12
     * @return This object instance for chaining
     */
    public function setFont($path, $size = 12, $color = '#000000')
    {
        $this->font = $path;
        $this->font_size = $size;
        $this->font_color = $color;
        
        return $this;
    }
    
    
    /**
     * @access public
     * @method generate Generates the captcha image
     * @param integer $lenght The length of the string defaults to 5
     * @param integer $difficulty the difficulty to use for the image, number between 1-5. Defaults to 3
     * @return string 
     */
    public function generate($length = 5, $difficulty = 3, $color = '#ffffff')
    {
        $angles = array(
            '15', '20',
            '25', '25',
            '30', '35',
            '40', '45',
            '50', '55',
            '60', '65',
            '70', '75'
        );
        
        $this->magick->newImage(($length*$this->font_size+$length*2), ($length*$this->font_size+$length*2), new \ImagickPixel($color));
        
        $this->magick->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $this->magick->setImageCompressionQuality(60);
        $this->magick->setImageFormat('jpeg');
        
        $this->drawer->setFont($this->font);
        $this->drawer->setFontSize($this->font_size);
        $this->drawer->setFillColor(new \ImagickPixel($this->font_color));
        
        $secret = substr(md5(uniqid(mt_rand(), true)), 0, $length);
        $_SESSION['security_captcha_session_variable'] = array('seceret' => $secret,
                                                                'ttl' => strtotime(date("d-n-Y H:i:s"), '+1 hour'));
        
        $this->magick->annotateImage($this->drawer, 25, 25, $angles[rand(0, (count($angles))-1)], $secret);
        $this->magick->swirlImage(($difficulty*4));
        for ($i = 0; $i < ($difficulty*3); $i++) {
            for ($j = 0; $j < ($difficulty*2); $j++) {
                $this->drawer->color(rand(1, ($this->image_w-7)), rand(1, ($this->image_h-7)), mt_rand(1, 4));
            }
            
            $this->drawer->line(rand(1, ($this->image_w-7)), rand(1, ($this->image_h-7)), rand(1, ($this->image_w-7)), rand(1, ($this->image_h-7)));
        }
        
        $filename = $this->save_path . DIRECTORY_SEPARATOR . md5(microtime()) . '.jpg';
        $fp = fopen($filename, "x");
        fwrite($fp, $this->magick->getImageBlob());
        fclose($fp);
        
        return $filename;
    }
    
    
    public function __destruct()
    {
        $this->drawer->destroy();
        $this->magick->destroy();
    }
    
}

?>