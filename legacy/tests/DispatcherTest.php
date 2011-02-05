<?php
require_once '../lib/functions/standard_lib.php';
require_once '../lib/classes/Dispatcher.php';

class DispatcherTest extends PHPUnit_Framework_TestCase {
	/**
	 * @expectedException Exception
	 */
	public function testBadController() {
		$D = new Dispatcher(md5(uniqid(rand(), true)));
	}
}
?>
