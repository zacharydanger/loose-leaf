<?php
namespace LooseLeaf\Tests;

require_once __DIR__ . '/helpers/Controller_TestSub.php';
require_once __DIR__ . '/../Controller.php';

class ControllerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @expectedException LooseLeaf\Redirect_Exception
	 */
	public function testRedirect_ThrowsException() {
		$C = new \Controller_TestSub();
		$C->subRedirect('/foobar.php');
	}

	public function testRedirect_ThrowsException_CheckLocation() {
		$C = new \Controller_TestSub();
		try {
			$C->subRedirect('/foobar.php');
			$this->fail("Should have thrown a Redirect_Exception.");
		} catch(\LooseLeaf\Redirect_Exception $e) {
			$this->assertEquals('/foobar.php', $e->getLocation());
		}
	}

	public static function assertSessionSet($key, $expected_value) {
		global $_SESSION;
		self::assertType('array', $_SESSION);
		self::assertEquals($expected_value, $_SESSION[$key]);
	}
}
?>