<?php
namespace noogic\tools;


class TemplateTest extends \PHPUnit_Framework_TestCase {
	protected $file;
	protected $content;
	protected $template;

	public function setUp(){
		$this->file = __DIR__ . '/json/config.json';
		$this->content = file_get_contents($this->file);
		$this->template = new Template($this->content);
	}

	public function test_Template_can_load_from_config(){
		$config = '{
			"a": 1
		}';

		$template = new Template($config);

		$this->assertSame($config, \PHPUnit_Framework_Assert::readAttribute($template, 'content'));
	}

	public function test_Template_can_load_from_file(){
		$this->assertSame(
			$this->content,
			\PHPUnit_Framework_Assert::readAttribute($this->template, 'content')
		);
	}

	public function test_Template_can_set_vars_as_array(){
		$vars = ['abcd', 3, -9];
		$this->template->setVars($vars);

		$this->assertSame($vars, \PHPUnit_Framework_Assert::readAttribute($this->template, 'values'));
	}

	public function test_Template_can_set_vars_as_key_value(){
		$vars = ['str' => 'abcd', 'int' => 3, 'negative' => -9];

		foreach ($vars as $key => $value) {
			$this->template->setVars($key, $value);
		}

		$this->assertSame($vars, \PHPUnit_Framework_Assert::readAttribute($this->template, 'values'));
	}

	public function test_Template_can_be_from_file(){
		$vars = ['var1' => 'mivar1', 'var2' => -99];
		$file = __DIR__ . '/json/template.tpl';

		$output = file_get_contents($file);

		foreach($vars as $key => $value){
			$tagToReplace = "[@$key]";
			$output = str_replace($tagToReplace, $value, $output);
		}

		$template = Template::fromFile($file);
		$template->setVars($vars);

		$this->assertSame($template->get(), $output);
	}

}
