<?php
namespace noogic\router;

use mocks\Basic;

class RouterTest extends \PHPUnit_Framework_TestCase {
	protected $router;

	public function setUp(){
		$this->router = new Router();
		$this->router->register('basic:get', function($params = null){
			$basic = new Basic();
			return $basic->get();
		});
	}

	/**
	 * Test that a router can register
	 */
	public function test_Router_can_register(){
		$this->assertArrayHasKey(
			'basic:get',
			\PHPUnit_Framework_Assert::readAttribute($this->router, 'methods')
		);
	}

	/*
	 * Test that a router can execute
	 */
	public function test_Router_can_execute(){
		$this->assertSame(true, $this->router->execute('basic:get'));
	}
}
