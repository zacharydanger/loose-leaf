<?php
require_once 'global.php';

class DBTest extends PHPUnit_Framework_TestCase {
	private $_DB;
	
	public function setUP() {
		DB::unsetDB();
		$this->_DB = DB::DB(TEST_DB_HOST, TEST_DB_USER, TEST_DB_PASS, TEST_DB_NAME);
	}
	
	public function testDB() {
		$DB = $this->_DB;
		$this->assertType("object", $DB);
	}
	
	public function testGetLink() {
		$link = $this->_DB->getLink();
		$this->assertType("object", $link);
		$this->assertEquals("mysqli", get_class($link));
	}
	
	/**
	 * @expectedException Exception
	 */
	public function testSetBadLink() {
		$this->_DB->setLink('asdf');
	}
	
	public function testSetGoodLink() {
		$this->_DB->setLink(new mysqli(TEST_DB_HOST, TEST_DB_USER, TEST_DB_PASS, TEST_DB_NAME));
	}
	
	public function tearDown() {
		DB::DB(TEST_DB_HOST, TEST_DB_USER, TEST_DB_PASS, TEST_DB_NAME);
	}
}
?>