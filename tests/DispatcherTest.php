<?php
require_once 'global.php';

class DispatcherTest extends PHPUnit_Framework_TestCase {
	/**
	 * @expectedException Exception
	 */
	public function testBadController() {
		$D = new Dispatcher(md5(uniqid(rand(), true)));
	}
}
?>
