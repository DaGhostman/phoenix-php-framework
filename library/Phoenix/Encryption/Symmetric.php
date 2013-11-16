<?php 
namespace Phoenix\Encryption;

use Phoenix\Application\Exception\Runtime;

class Symmetric {
	
	private $alg = MCRYPT_RIJNDAEL_256, $mode = MCRYPT_MODE_CBC;
	private $init = null, $key = null, $vector = null;
	public function __construct($alg = null, $mode = MCRYPT_MODE_CBC)
	{
		if (!in_array('mcrypt', get_loaded_extensions()))
		{
			throw new Runtime('The MCrypt extension is not loaded.', null, null);
		}
		
		$this->mode = $mode;
		$this->init = mcrypt_module_open($algorithm, null, $mode);
	}
	
	public function encrypt($data, $key = null, $vector = null)
	{
		$this->key = ($key ? 
			$key : mcrypt_create_iv(mcrypt_get_key_size($this->init), MCRYPT_DEV_URANDOM));
		$this->vector = ($vector ? 
			$vector : mcrypt_create_iv(mcrypt_get_iv_size($this->init, $this->mode), MCRYPT_DEV_URANDOM));
		
		mcrypt_generic_init($this->init, $this->key, $this->vector);
		return mcrypt_generic($this->init, $data);
	}
	
	public function getEncryptionKey()
	{
		return $this->key;
	}
	
	public function getInitialisationVector()
	{
		return $this->vector;
	}
	
	public function decrypt($data, $key = null, $vector = null)
	{
		$k = ($key ? $key : $this->key);
		$iv = ($vector ? $vector : $this->vector);
		
		return mcrypt_decrypt($this->alg, $k, $data, $this->mode, $iv);
	}
	
	
	public function __destruct()
	{
		mcrypt_generic_deinit($this->init);
		mcrypt_module_close($this->init);
	}
	
}
?>