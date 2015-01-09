<?php
namespace noogic\tools;

use noogic\mocks\Basic;

class CollectionTest extends \PHPUnit_Framework_TestCase {
	protected $collection;
	protected $itemsCollection;
	protected $items;

	public function setUp(){
		$this->collection = new Collection();
		$this->itemsCollection = new Collection();

		$this->items = ['a' => new Basic(1), 'b' => new Basic(2), 'c' => new Basic(3)];

		foreach ($this->items as $key => $value) {
			$this->itemsCollection->add($value, $key);
		}
	}



	public function test_Collection_can_add_items(){
		$item = new Basic();

		$this->collection->add($item, 0);
		$items = \PHPUnit_Framework_Assert::readAttribute($this->collection, 'items');

		$this->assertSame($item, $items[0]);
	}


	public function test_Collection_throws_exception_on_duplicate_key(){
		$item = new Basic();

		$this->collection->add($item, 'a');

		$this->setExpectedException('\Exception', "This collection already has key a");
		$this->collection->add($item, 'a');
	}


	public function test_Collection_can_get_an_item(){
		$item = new Basic(1);

		$this->collection->add($item);

		$this->assertSame($item, $this->collection->get(0));
	}


	public function test_Collection_can_get_all_items(){
		$items = [new Basic(1), new Basic(2)];

		$this->collection->add($items[0]);
		$this->collection->add($items[1]);

		$this->assertSame($items, $this->collection->get());
	}


	public function test_Collection_gets_null_on_non_existent_item_request(){
		$this->assertNull($this->collection->get('a'));
	}


	public function test_Collection_tells_if_has_a_key(){
		$this->collection->add(new Basic(), 'z');

		$this->assertTrue($this->collection->has('z'));
		$this->assertFalse($this->collection->has('x'));
	}


	public function test_Collection_can_update(){
		$items = [new Basic(1), new Basic(2)];

		$this->collection->add($items[0], 'a');
		$this->assertSame($items[0], $this->collection->get('a'));
		$this->collection->update($items[1], 'a');
		$this->assertSame($items[1], $this->collection->get('a'));
	}


	public function test_Collection_throws_error_on_update_non_existing_key(){
		$item = new Basic(1);
		$key = 'c';

		$this->setExpectedException('Exception', "Key $key doesn't exists");

		$this->collection->update($item, $key);
	}


	public function test_Collection_can_create_key_on_update_with_non_existent_key(){
		$item = new Basic(1);
		$key = 'c';

		$this->collection->update($item, $key, true);
		$this->assertSame($item, $this->collection->get($key));
	}


	public function test_Collection_can_update_on_add_duplicate_key(){
		$items = [new Basic(), new Basic()];

		$this->collection->add($items[0], 'a');
		$this->collection->add($items[1], 'a', true);

		$this->assertSame($items[1], $this->collection->get('a'));
	}


	public function test_Collection_is_countable(){
		$this->assertInstanceOf('\Countable', $this->collection);
	}


	public function test_Collection_counts(){
		for($i = 0; $i < 3; $i++){
			$this->collection->add(new Basic($i));
		}

		$this->assertCount(3, $this->collection);
	}


	public function test_Collection_can_delete(){
		$items = [new Basic(0), new Basic(1)];

		$this->collection->add($items[0], 'a');
		$this->collection->add($items[1], 'b');

		$this->collection->delete('b');

		$this->assertCount(1, $this->collection);
		$this->assertFalse($this->collection->has('b'));

		$this->assertNull($this->collection->get('b'));
	}

	public function test_Collection_gives_keys(){
		$keys = ['a', 'b', 'c'];

		foreach ($keys as $key) {
			$this->collection->add(new Basic(), $key);
		}

		$this->assertSame($keys, $this->collection->keys());
	}

	public function test_Collection_can_be_created_with_another_collection(){
		$collection = new Collection($this->itemsCollection);
		$this->assertSame($collection->get(), $this->itemsCollection->get());
	}

	public function test_Collection_hash_each_lambda(){
		$this->itemsCollection->each(function($value){
			$this->assertInstanceOf('\noogic\mocks\basic', $value);
		});
	}

	public function test_Collection_gives_first_element(){
		$basic = $this->itemsCollection->first();
		$this->assertInstanceOf('\noogic\mocks\Basic', $basic);

		$basic = $this->collection->first();
		$this->assertNull($basic);
	}

	public function test_Collection_can_be_used_as_an_array(){
		$this->assertSame($this->items['a'], $this->itemsCollection['a']);
	}

	public function test_Collection_is_iterable(){
		foreach($this->collection as $key => $value){
			$this->assertSame($this->items[$key], $value);
		}
	}
}
