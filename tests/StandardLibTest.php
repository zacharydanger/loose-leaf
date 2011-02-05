<?php
require_once __DIR__ . '/../standard_lib.php';

class StandardLibTest extends PHPUnit_Extensions_OutputTestCase {
	public function setUp() {
		global $_SESSION, $_GET;
		$_SESSION = array();
		$_GET = array();
	}

	public function testRequestVar_NotFound() {
		global $_REQUEST;
		$_REQUEST = array();
		$this->assertNull(request_var('foo'));
	}

	public function testRequestVar_NotFound_DefaultVal() {
		global $_REQUEST;
		$_REQUEST = array();
		$default = sha1(microtime());
		$this->assertEquals($default, request_var('foo', $default));
	}

	public function testRequestVar_Found() {
		global $_REQUEST;
		$key = sha1(microtime());
		$val = md5(microtime());
		$_REQUEST = array($key => $val);
		$this->assertEquals($val, request_var($key));
	}

	public function tearDown() {
		global $_POST;
		$_POST = array();
	}
}

?>
