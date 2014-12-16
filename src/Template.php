<?php
namespace tools;


use SebastianBergmann\Exporter\Exception;

class Template {
	protected $content;
	protected $values = [];

	public function __construct($content){
		$this->content = $content;
	}

	public static function fromFile($file){
		$content = file_get_contents($file);

		if($content === FALSE)
			throw new \Exception("Invalid file: $file");

		return new self($content);
	}

	public function setVars($key, $value = null){
		if(is_array($key))
			$this->values = $key;
		else {
			if(is_array($value)) throw new Exception("Values can't be an array");
			$this->values[$key] = $value;
		}
	}

	public function get(){
		$output = $this->content;

		foreach($this->values as $key => $value){
			$tagToReplace = "[@$key]";
			$output = str_replace($tagToReplace, $value, $output);
		}

		return $output;
	}
}
