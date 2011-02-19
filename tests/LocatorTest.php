<?php
namespace LooseLeaf\Tests;

require_once __DIR__ . '/../Locator.php';

use \LooseLeaf\Locator;

class LocatorTest extends \PHPUnit_Framework_TestCase {
	public function setUp() {
		Locator::reset();
	}

	public function tearDown() {
		Locator::reset();
	}

	public function testGetInstance() {
		$this->assertTrue(Locator::get() instanceof Locator);
	}

	public function testFind_Empty() {
		$this->assertNull(Locator::find('foobar'));
	}

	public function testFind_KeySet() {
		Locator::set('foo', 'bar');
		$this->assertEquals('bar', Locator::find('foo'));
	}

	public function testReset() {
		Locator::set('foo', 'bar');
		$this->assertEquals('bar', Locator::find('foo'));
		Locator::reset();
		$this->assertNull(Locator::find('foo'));
	}

	/**
	 * @expectedException \PHPUnit_Framework_Error
	 */
	public function testSetDefault_NonFunction() {
		Locator::setDefault('foo', 'bar');
	}

	public function testSetDefault() {
		Locator::setDefault('foo', function() {
			return 'WTF';
		});

		$this->assertEquals('WTF', Locator::find('foo'));
	}

	public function testSetDefault_SetsValueInStore() {
		Locator::setDefault('foo', function() {
			return 'WTF';
		});

		$this->assertEquals('WTF', Locator::find('foo'));

		$resource_dump = Locator::dump();
		$this->assertEquals('WTF', $resource_dump['foo']);
	}
}
