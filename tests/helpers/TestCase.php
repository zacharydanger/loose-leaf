<?php
namespace LooseLeaf\Tests;

require_once __DIR__ . '/Mock_PDO.php';

class TestCase extends \PHPUnit_Framework_TestCase {
	public function getMockPDO() {
		return $this->getMock('Mock_PDO', array('prepare', 'lastInsertId'));
	}

	public function getMockPDOStatement() {
		return $this->getMock('\Std_Class', array('bindValue', 'execute', 'fetchAll'));
	}

	public static function assertRedirect($url, $request) {
		$expected = new \LooseLeaf\Redirect_Request($url);
		self::assertEquals($expected, $request);
	}

	public function setPost($post_array = array()) {
		global $_POST;
		$_POST = $post_array;
	}
}
