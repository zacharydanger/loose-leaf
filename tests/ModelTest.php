<?php
namespace LooseLeaf\Tests;

require_once __DIR__ . '/../Model.php';

use LooseLeaf\Model;

class Model_TestSub extends Model {
	protected $data = array(
		'foo' => null,
		'bar' => 'foo',
		'qwerty' => ' asdf '
	);

	public function _get_qwerty() {
		return trim($this->data['qwerty']);
	}

	public function _set_bar($new_value) {
		return $new_value . $new_value;
	}
}

class Foo {
	private $bar;
	public $foo;
}

class ModelTest extends \PHPUnit_Framework_TestCase {
	public function testSetter_GoodField() {
		$MT = new Model_TestSub();
		$MT->foo = 'bar';
		$this->assertEquals('bar', $MT->foo);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testSetter_BadField() {
		$MT = new Model_TestSub();
		$MT->asdf = 'foobar';
	}

	public function testGetter_BadField() {
		$MT = new Model_TestSub();
		$this->assertNull($MT->asdf);
	}

	public function testGetter_Default() {
		$MT = new Model_TestSub();
		$this->assertEquals('foo', $MT->bar);
	}

	public function testIsset_DefaultNull() {
		$MT = new Model_TestSub();
		$this->assertFalse(isset($MT->foo));
	}

	public function testIsset_DefaultTrue() {
		$MT = new Model_TestSub();
		$this->assertTrue(isset($MT->bar));
	}

	public function testGetOverride() {
		$MT = new Model_TestSub();
		$this->assertEquals('asdf', $MT->qwerty);
	}

	public function testSetOverride() {
		$MT = new Model_TestSub();
		$MT->bar = 'foobar';
		$this->assertEquals('foobarfoobar', $MT->bar);
	}

	public function testLoad() {
		$MT = new Model_TestSub();
		$MT->load(array(
			'foo' => 13,
			'bar' => 37,
			'qwerty' => 'qwerty')
		);

		$this->assertEquals(13, $MT->foo);
		$this->assertEquals('3737', $MT->bar);
		$this->assertEquals('qwerty', $MT->qwerty);
	}
}
