<?php
require_once 'global.php';
require_once 'classes/Object.php';
require_once 'inc/TestObject.php';

class ObjectTest extends PHPUnit_Framework_TestCase {
	protected $o;

	public function setUp() {
		$this->o = new Object();
	}

	public function testGetHooks() {
		$O = new TestObject();
		$this->assertEquals("#bar#", $O->foo);
		$this->assertEquals("@#foo#@", $O->bar);
	}

	public function testSetHooks() {
		$O = new TestObject();
		$O->foo = 'bar';
		$this->assertEquals("#BAR#", $O->foo);
	}

	public function testListKeys() {
		$O = new TestObject();
		$keys = $O->listKeys();
		$this->assertType("array", $keys);
		$this->assertGreaterThan(0, count($keys));
	}

	public function testValidators() {
		$O = new TestObject();
		$value = sha1(time() . rand(10,99));
		$O->asdf = $value;
		$this->assertNotEquals($value, $O->asdf);
		$O->asdf = 'qwerty';
		$this->assertEquals('qwerty', $O->asdf);

		$O->qwerty = $value;
		$this->assertNotEquals($value, $O->qwerty);

		$O->qwerty = 'asdf';
		$this->assertEquals('asdf', $O->qwerty);
	}

	public function testNewObjectExists() {
		$O = new Object();
		$this->assertFalse($O->exists());
	}

	public function testFakeObjectExists() {
		$O = new Object('foobar');
		$this->assertFalse($O->exists());
	}

	public function testLoadFromGoodArray() {
		$new_data = array('asdf' => 'qwerty');
		$O = new TestObject();
		$O->load($new_data);
		$this->assertEquals($new_data['asdf'], $O->asdf);
	}

	public function testLoadFromBadArray() {
		$new_data = array('asdf' => 'qwerty');
		$new_data['foooobar'] = 'some_value';
		$O = new Test_Object_Foo();
		$O->load($new_data);
		$this->assertEquals($new_data['asdf'], $O->asdf);
		$this->assertFalse(isset($O->foooobar));
	}

	/**
	 * @expectedException Exception
	 */
	public function testLoadBadInput() {
		$O = new Test_Object_Foo();
		$O->load('asdf');
	}

	public function provider() {
		return array(
			array(0, 0, 0),
			array(0, 1, 1),
			array(1, 0, 1),
			array(1, 1, 3)
		);
	}
}
?>