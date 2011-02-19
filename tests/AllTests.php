<?php
require_once __DIR__ . '/RouterTest.php';
require_once __DIR__ . '/ControllerTest.php';
require_once __DIR__ . '/DispatcherTest.php';
require_once __DIR__ . '/RedirectorTest.php';
require_once __DIR__ . '/ControllerLocatorTest.php';
require_once __DIR__ . '/StandardLibTest.php';
require_once __DIR__ . '/HtmlTemplateTest.php';
require_once __DIR__ . '/ModelTest.php';
require_once __DIR__ . '/DataMapperTest.php';
require_once __DIR__ . '/LocatorTest.php';

class LooseLeaf_AllTests {
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('LooseLeaf - AllTests');
		$suite->addTestSuite('RouterTest');
		$suite->addTestSuite('\LooseLeaf\Tests\ControllerTest');
		$suite->addTestSuite('DispatcherTest');
		$suite->addTestSuite('RedirectorTest');
		$suite->addTestSuite('ControllerLocatorTest');
		$suite->addTestSuite('StandardLibTest');
		$suite->addTestSuite('HtmlTemplateTest');
		$suite->addTestSuite('\LooseLeaf\Tests\ModelTest');
		$suite->addTestSuite('\LooseLeaf\Tests\DataMapperTest');
		$suite->addTestSuite('\LooseLeaf\Tests\LocatorTest');
		return $suite;
	}
}
?>
