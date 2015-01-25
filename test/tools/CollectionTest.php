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

	public function test_Collection_can_be_created_with_an_array(){
		$collection = new Collection($this->items);

		$this->assertSame($this->items, $collection->get());
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

	public function test_Collection_can_set_valid_types(){
		$classType = [get_class($this->collection)];
		$collection = new Collection(null, $classType);
		$collectionValidTypes = \PHPUnit_Framework_Assert::readAttribute($collection, 'validTypes');

		$this->assertEquals($collectionValidTypes, $classType);

		$this->setExpectedException('\InvalidArgumentException', 'Item must be an object because there are object type restrictions');
		$collection->add([]);

		$this->setExpectedException('\InvalidArgumentException', 'Item type is not valid');
		$obj = json_decode(json_encode([]));
		$this->collection->add($obj);
	}

	public function test_Collection_can_be_compared_to_another_collection(){
		$collections[] = new Collection();
		$collections[0]->add('a');

		$collections[] = new Collection();
		$collections[1]->add('a');

		$this->assertTrue(Collection::compare($collections));
	}

	public function test_Collection_is_found_in_array_of_collections(){
		$collections = new Collection();

		$collections[] = new Collection();
		$collections[0]->add('a');

		$collections[] = new Collection();
		$collections[1]->add('b');

		//$finalCollection = new Collection('b');
	}

	public function test_Collection_adds_null_values(){
		$collection = new Collection();

		$collection[] = null;

		$this->assertCount(1, $collection);
		$this->assertEquals(null, $collection[0]);
	}

	public function test_Collection_is_able_to_not_allow_null_values(){
		$collection = new Collection();
		$collection->add(null, null, false, false);

		$this->assertCount(0, $collection);
	}

	public function test_Collection_can_find_an_element(){
		$collection = new Collection();
		$collection->add(1, 'a');
		$collection->add(2, 'b');
		$collection->add(2, 'c');

		$expected = ['b', 'c'];

		$this->assertSame($expected, $collection->find(2)->get());
	}
}
