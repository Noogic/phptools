<?php
namespace noogic\tools;


class ConfigTest extends \PHPUnit_Framework_TestCase {
	protected $config;
	protected $path;
	protected $json;

	public function setUp(){
		$this->config = new Config();
		$this->path = __DIR__ . '/json/config.json';
		$this->json = '
		{
			"a": [3, 4, -9],
			"b": {
				"x": 1,
				"y": -1
			}
		}
		';
	}

	public function test_Config_can_be(){
		$this->assertInstanceOf('\noogic\tools\Config', new Config());
	}

	public function test_Config_can_load_from_json(){


		$obj = json_decode($this->json);

		$this->assertEquals($obj, $this->config->load($this->json));
	}

	public function test_Config_can_load_from_file_based_on_constructor_path(){
		$config = new Config($this->path);
		$obj = json_decode(file_get_contents($this->path));

		$this->assertEquals($obj, $config->loadFromFile());
	}

	public function test_Config_can_load_from_filed_based_on_a_given_path(){
		$config = new Config();
		$obj = json_decode(file_get_contents($this->path));

		$this->assertEquals($obj, $config->loadFromFile($this->path));
	}

	public function test_Config_gives_access_to_path(){
		// Based on constructor path
		$path = $this->path;
		$config = new Config($path);
		$this->assertSame($path, $config->path);

		// Based on loadFromFilePath
		$config = new Config();
		$config->loadFromFile($this->path);
		$this->assertSame($this->path, $config->path);
	}

	public function test_Config_gives_access_to_json(){
		$config = new Config();
		$config->load($this->json);

		$this->assertSame($this->json, $config->json);
	}

	public function test_Config_gives_access_to_object(){
		$this->config->load($this->json);
		$this->assertEquals([3, 4, -9], $this->config->a);
	}
}
 