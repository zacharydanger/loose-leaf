<?php
require_once __DIR__ . '/RouterTest.php';
require_once __DIR__ . '/ControllerTest.php';
require_once __DIR__ . '/DispatcherTest.php';
require_once __DIR__ . '/RedirectorTest.php';
require_once __DIR__ . '/ControllerLocatorTest.php';
require_once __DIR__ . '/StandardLibTest.php';
require_once __DIR__ . '/HtmlTemplateTest.php';

class LooseLeaf_AllTests {
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('HabitMakr - AllTests');
		$suite->addTestSuite('RouterTest');
		$suite->addTestSuite('ControllerTest');
		$suite->addTestSuite('DispatcherTest');
		$suite->addTestSuite('RedirectorTest');
		$suite->addTestSuite('ControllerLocatorTest');
		$suite->addTestSuite('StandardLibTest');
		$suite->addTestSuite('HtmlTemplateTest');
		return $suite;
	}
}
?>