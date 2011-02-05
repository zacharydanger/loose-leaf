<?php
require_once dirname(__FILE__) . '/../Dispatcher.php';
require_once dirname(__FILE__) . '/helpers/Dispatcher_Test_Controller.php';
require_once dirname(__FILE__) . '/helpers/Controller_TestSub.php';
require_once __DIR__ . '/helpers/Concrete_Renderable.php';

class DispatcherTest extends \PHPUnit_Extensions_OutputTestCase {
	public function setUp() {
		LooseLeaf\Redirector::unsetOverride();
	}

	/**
	 * @expectedException LooseLeaf\Controller_Not_Found_Exception
	 */
	public function testBadController() {
		$D = new LooseLeaf\Dispatcher(sha1(microtime()));
	}

	public function testDispatch_RedirectException() {
		$mock_redirector = $this->getMock('Std_Class', array('redirect'));
		$mock_redirector->expects($this->once())
			->method('redirect')
			->with('/foobar.php');
		LooseLeaf\Redirector::useRedirector($mock_redirector);

		$D = new LooseLeaf\Dispatcher('Controller_TestSub', 'subRedirect', '/foobar.php');
		$D->dispatch();
	}

	public function testDispatch_RedirectRequest() {
		$url = '/' . sha1(microtime()) . '/' . md5(microtime()) . '/';
		$mock_redirector = $this->getMock('Std_Class', array('redirect'));
		$mock_redirector->expects($this->once())
			->method('redirect')
			->with($url);
		LooseLeaf\Redirector::useRedirector($mock_redirector);
		$D = new LooseLeaf\Dispatcher('Controller_TestSub', 'redirRequest', $url);
		$D->dispatch();
	}

	public function testDispatch_ControllerReturns() {
		$controller = 'Dispatcher_Test_Controller';
		$action = 'returnSomething';

		$this->expectOutputString(Controller_TestSub::returnSomething());
		$D = new LooseLeaf\Dispatcher('Controller_TestSub', 'returnSomething', null);
		$D->dispatch();
	}

	public function testDispatch_Renderable() {
		$render_me = $this->getMock('Concrete_Renderable');
		$render_me->expects($this->once())
			->method('render');

		$mock_controller = $this->getMock('Std_Class', array('returnRenderable'));
		$mock_controller->expects($this->once())
			->method('returnRenderable')
			->will($this->returnValue($render_me));

		LooseLeaf\Controller_Locator::get()->setController('Dispatcher_Test_Controller', $mock_controller);

		$D = new LooseLeaf\Dispatcher('Dispatcher_Test_Controller', 'returnRenderable', null);
		$D->dispatch();
	}
}
