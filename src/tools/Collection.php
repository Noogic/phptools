<?php
namespace noogic\tools;


use SebastianBergmann\Exporter\Exception;

class Collection implements \Countable, \ArrayAccess, \Iterator{
	protected $items = [];
	protected $validTypes = null;

	function __construct($collection = null, $validTypes = null){
		if(is_array($collection)){
			$this->items = $collection;
		}
		else if(is_a($collection, 'noogic\tools\Collection')){
			$this->items = $collection->get();
		}

		if($validTypes){
			$this->setValidTypes($validTypes);
		}
	}


	public function add($item, $key = null, $updateOnDuplicate = false, $allowNull = true){
		$this->validateItemType($item);

		if(!$item AND !$allowNull)
			return ;

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
			$fn($value, $key);
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

	private function setValidTypes($validTypes){
		if(!is_array($validTypes))
			throw new \InvalidArgumentException("Valid types must be an array");

		$this->validTypes = $validTypes;
	}

	protected function validateItemType($item){
		$validTypes = $this->validTypes;

		if($validTypes){
			if(!is_object($item))
				throw new \InvalidArgumentException('Item must be an object because there are object type restrictions');

			$objectType = get_class($item);
			$type_is_invalid = !in_array($objectType, $validTypes);

			if($type_is_invalid)
				throw new \InvalidArgumentException('Item type is not valid');
		}
	}

	public static function compare($collections){
		if(!is_array($collections))
			throw new \InvalidArgumentException("Collections must be an array");

		$minimumComparableCollections = 2;
		$numberOfCollections = count($collections);

		if($numberOfCollections < $minimumComparableCollections)
			throw new \InvalidArgumentException("You need at least two collection to compare them");

		$currentCollection = array_shift($collections);

		foreach($collections as $collection){
			if($collection != $currentCollection){
				return false;
			}

			$currentCollection = $collection;
		}

		return true;
	}

	/** ArrayAccess */
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
	/**  */


	/** Iterator */
	public function rewind() {
		reset($this->items);
	}

	public function current() {
		return current($this->items);
	}

	public function key() {
		return key($this->items);
	}

	public function next() {
		next($this->items);
	}

	public function valid() {
		return key($this->items) !== null;
	}
}
