<?php
namespace noogic\tools;


class Collection implements \Countable, \ArrayAccess{
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

	public function __get($key){
		return $this->get($key);
	}

	public function update($item, $key, $createOnEmpty = false){
		if(array_key_exists($key, $this->items) OR $createOnEmpty)
			$this->items[$key] = $item;
		else
			throw new \Exception("Key $key doesn't exists");
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

	public function each($fn){
		foreach($this->items as $key => $value){
			$fn($key, $value);
		}
	}

	public function first(){
		$values = array_values($this->items);

		return isset($values[0]) ? $values[0] : null;
	}

	public function random(){
		return $this->items[array_rand($this->items)];
	}

	public function randomCallback($fn){
		$items = [];
		foreach ($this->items as $key => $value) {
			$res = $fn($value);
			if($res) {
				$items[$key] = $res;
			}
		}

		return count($items) ? $items[array_rand($items)] : null;
	}

	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->items[] = $value;
		} else {
			$this->items[$offset] = $value;
		}
	}

	public function offsetExists($offset) {
		return isset($this->items[$offset]);
	}

	public function offsetUnset($offset) {
		unset($this->items[$offset]);
	}

	public function offsetGet($offset) {
		return isset($this->items[$offset]) ? $this->items[$offset] : null;
	}
}
