<?php
require_once dirname(__FILE__) . '/../Redirector.php';

class RedirectorTest extends PHPUnit_Framework_TestCase {
	public function testUseRedirector() {
		$mock_redir = $this->getMock('Std_Class');
		Redirector::useRedirector($mock_redir);
		$this->assertEquals($mock_redir, Redirector::get());
	}

	public function testUnsetOverride() {
		$mock_redir = $this->getMock('Std_Class');
		Redirector::useRedirector($mock_redir);
		$this->assertEquals($mock_redir, Redirector::get());
		Redirector::unsetOverride();
		$this->assertTrue(is_a(Redirector::get(), 'Redirector'));
	}

	public function tearDown() {
		Redirector::unsetOverride();
	}
}
?>