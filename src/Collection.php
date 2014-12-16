<?php
namespace noogic\tools;

use SebastianBergmann\Exporter\Exception;

class Collection implements \Countable{
	protected $items = [];
	protected $validTypes = null;

	function __construct($collection = null){
		$this->items = $collection ? $collection->get() : [];
	}

	public function add($item, $key = null, $updateOnDuplicate = false){
		if($key === null)
			$this->items[] = $item;
		else {
			if($this->has($key) AND $updateOnDuplicate === false)
				throw new \Exception("This collection already has key $key");
		else
			$this->items[$key] = $item;
		}
	}

	public function get($key = null){
		if($key !== null){
			if(array_key_exists($key, $this->items))
				return $this->items[$key];
			else return null;
		}
		else
			return $this->items;
	}

	public function update($item, $key, $createOnEmpty = false){
		if(array_key_exists($key, $this->items) OR $createOnEmpty)
			$this->items[$key] = $item;
		else
			throw new Exception("Key $key doesn't exists");
	}

	public function has($key){
		return array_key_exists($key, $this->items);
	}

	public function keys(){
		return array_keys($this->items);
	}

	public function count(){
		return count($this->items);
	}

	public function delete($key){
		unset($this->items[$key]);
	}
}