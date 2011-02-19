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
}
