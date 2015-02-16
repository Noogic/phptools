<?php

namespace noogic\tools;


class Config {
	protected $_path = null;
	protected $_json = null;
	protected $_obj  = null;


	function __construct($path = null){
		if($path) {
			$this->_path = defined('_ROOTPATH_') ? _ROOTPATH_ . '/config/' . $path : $path;
			$this->loadFromFile();
		}

	}

	protected function setPath(){
		$this->_path = null;
	}
	/**
	 * @param null $json
	 * @return \stdClass the json object
	 * @throws \Exception on invalid Json
	 */
	public function load($json = null){
		$this->_json = $json ?: $this->_json;
		if(!$json) throw new \Exception("Invalid json: $this->_json");

		$this->_obj = json_decode($this->_json);

		return $this->_obj;
	}

	/**
	 * @param null $path
	 * @return \stdClass
	 * @throws \Exception if @load throws it
	 */
	public function loadFromFile($path = null){
		$this->_path = $path ?: $this->_path;
		$json = file_get_contents($this->_path);

		return $this->load($json);
	}

	function __get($arg){
		switch($arg){
			case 'path':
				return $this->_path;
				break;

			case 'json':
				return $this->_json;
				break;

			default:
				return isset($this->_obj->$arg) ? $this->_obj->$arg : null;
				break;
		}
	}

}
