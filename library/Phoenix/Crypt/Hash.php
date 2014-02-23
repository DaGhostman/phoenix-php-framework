<?php

namespace Phoenix\Crypt;

class Hash {
	
	const DEF_ALG = 'whirlpool';
	protected $chunking = array(
	  'allowed' => false,
	  'length' => 64,
	  'ln_end' => '\0x0000'
	);
	protected $alg;
	
	public function __construct($algo = null)
	{
		if (!empty($algo) | !in_array($algo, hash_algos()))
		{
			$this->alg = Hash::DEF_ALG;
		} else {
			$this->alg = $algo;
		}
	}
	
	public function dataChunking($enabled, $length = 64, $delimiter = '\0x0000')
	{
		$this->chunking['allowed'] = $enabled;
		$this->chunking['length'] = $length;
		$this->chunking['ln_end'] = $delimiter;
		
		return $this;
	}
	
	public function hash($data) {
		switch($this->chunking['allowed']) {
			case true:
				$chunks = explode($this->chunking['ln_end'], 
								chunk_split($data, 
								$this->chunking['length'], 
								$this->chunking['ln_end'])
								);
								
				$init = hash_init($this->alg);
				foreach($chunks as $chunk) {
					hash_update($init, $chunk);
				}
				return hash_final($init, false);
				break;
			case false:
				return hash($this->alg, $data);
				break;
			default:
				return hash($this->alg, $data);
				break;
		}
	}
	
	public function hash_file($file) {
		return hash_file($this->alg, $file);
	}
	
	public function hmac_hash($data, $key) {
	switch($this->chunking['allowed']) {
			case true:
				$chunks = explode($this->chunking['ln_end'], 
								chunk_split($data, 
								$this->chunking['length'], 
								$this->chunking['ln_end'])
								);
								
				$init = hash_init($this->alg, HASH_HMAC, $key);
				foreach($chunks as $chunk) {
					hash_update($init, $chunk);
				}
				return hash_final($init, false);
				break;
			case false:
				return hash_hmac($this->alg, $data, $key);
				break;
			default:
				return hash_hmac($this->alg, $data, $key);
				break;
		}
	}
	
	public function hmac_file($file, $key) {
		return hash_hmac_file($this->alg, $file, $key);
	}
}

?>