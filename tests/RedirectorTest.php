<?php
require_once dirname(__FILE__) . '/../Redirector.php';

class RedirectorTest extends \PHPUnit_Framework_TestCase {
	public function testUseRedirector() {
		$mock_redir = $this->getMock('Std_Class');
		LooseLeaf\Redirector::useRedirector($mock_redir);
		$this->assertEquals($mock_redir, LooseLeaf\Redirector::get());
	}

	public function testUnsetOverride() {
		$mock_redir = $this->getMock('Std_Class');
		LooseLeaf\Redirector::useRedirector($mock_redir);
		$this->assertEquals($mock_redir, LooseLeaf\Redirector::get());
		LooseLeaf\Redirector::unsetOverride();
		$this->assertTrue(LooseLeaf\Redirector::get() instanceof LooseLeaf\Redirector);
	}

	public function tearDown() {
		LooseLeaf\Redirector::unsetOverride();
	}
}
?>