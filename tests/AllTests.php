<?php
require_once 'PHPUnit/Framework.php';
require_once 'ImageTest.php';
require_once 'MessageStackTest.php';
require_once 'RouterTest.php';
require_once 'DispatcherTest.php';

class AllTests {
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('Loose-Leaf');
		$suite->addTestSuite('ImageTest');
		$suite->addTestSuite('MessageStackTest');
		$suite->addTestSuite('RouterTest');
		$suite->addTestSuite('DispatcherTest');
		return $suite;
	}
}
?>