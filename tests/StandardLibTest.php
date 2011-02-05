<?php
require_once __DIR__ . '/../standard_lib.php';

class StandardLibTest extends \PHPUnit_Extensions_OutputTestCase {
	public function setUp() {
		global $_POST, $_SESSION, $_GET, $_REQUEST;
		$_POST = array();
		$_GET = array();
		$_SESSION = array();
		$_REQUEST = array();
	}

	public function tearDown() {
		global $_POST, $_SESSION, $_GET, $_REQUEST;
		$_POST = array();
		$_GET = array();
		$_SESSION = array();
		$_REQUEST = array();
	}

	public function testRequestVar_NotFound() {
		global $_REQUEST;
		$_REQUEST = array();
		$this->assertNull(LooseLeaf\request_var('foo'));
	}

	public function testRequestVar_NotFound_DefaultVal() {
		global $_REQUEST;
		$_REQUEST = array();
		$default = sha1(microtime());
		$this->assertEquals($default, LooseLeaf\request_var('foo', $default));
	}

	public function testRequestVar_Found() {
		global $_REQUEST;
		$key = sha1(microtime());
		$val = md5(microtime());
		$_REQUEST = array($key => $val);
		$this->assertEquals($val, LooseLeaf\request_var($key));
	}

	public function testGetVar_NotFound() {
		global $_GET;
		$_GET = array();
		$this->assertNull(LooseLeaf\get_var('foo'));
	}

	public function testGetVar_NotFound_DefaultVal() {
		global $_GET;
		$_GET = array();
		$default = sha1(microtime());
		$this->assertEquals($default, LooseLeaf\get_var('foo', $default));
	}

	public function testGetVar_Found() {
		global $_GET;
		$key = sha1(microtime());
		$val = md5(microtime());
		$_GET = array($key => $val);
		$this->assertEquals($val, LooseLeaf\get_var($key));
	}

	public function testPostVar_NotFound() {
		global $_POST;
		$_POST = array();
		$this->assertNull(LooseLeaf\post_var('foo'));
	}

	public function testPostVar_NotFound_DefaultVal() {
		global $_POST;
		$_POST = array();
		$default = sha1(microtime());
		$this->assertEquals($default, LooseLeaf\post_var('foo', $default));
	}

	public function testPostVar_Found() {
		global $_POST;
		$key = sha1(microtime());
		$val = md5(microtime());
		$_POST = array($key => $val);
		$this->assertEquals($val, LooseLeaf\post_var($key));
	}

	public function testSessionVar_NotFound() {
		global $_SESSION;
		$_SESSION = array();
		$this->assertNull(LooseLeaf\session_var('foo'));
	}

	public function testSessionVar_NotFound_DefaultVal() {
		global $_SESSION;
		$_SESSION = array();
		$default = sha1(microtime());
		$this->assertEquals($default, LooseLeaf\session_var('foo', $default));
	}

	public function testSessionVar_Found() {
		global $_SESSION;
		$key = sha1(microtime());
		$val = md5(microtime());
		$_SESSION = array($key => $val);
		$this->assertEquals($val, LooseLeaf\session_var($key));
	}
}

?>
