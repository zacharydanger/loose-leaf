<?php
require_once 'PHPUnit/Framework.php';
require_once 'ImageTest.php';

class AllTests {
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('Loose-Leaf');
		$suite->addTestSuite('ImageTest');
		return $suite;
	}
}
?>