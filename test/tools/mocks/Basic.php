<?php

namespace noogic\mocks;


class Basic {
	public $default = [];

	function __construct($item = null){
		if($item) $this->default[] = $item;
	}

	public function get(){
		return true;
	}
}