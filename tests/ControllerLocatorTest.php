<?php
require_once __DIR__ . '/helpers/Controller_TestSub.php';
require_once __DIR__ . '/../Controller_Locator.php';

class ControllerLocatorTest extends PHPUnit_Framework_TestCase {
	public function testFindController() {
		$this->assertEquals(new Controller_TestSub(), Controller_Locator::get()->findController('Controller_TestSub'));
	}

	public function testSetController() {
		$fake_controller = array("WTF?");
		Controller_Locator::get()->setController('Foo_Controller', $fake_controller); 
		$this->assertEquals($fake_controller, Controller_Locator::get()->findController('Foo_Controller'));
	}
}
?>
