<?php
require_once 'PHPUnit/Framework.php';
require_once '../lib/classes/Controller.php';

class ControllerTest_Controller extends Controller {
	/* stub class for testing */
	public function __construct(Template $template, View $view) {
		$this->_setTemplate($template);
		$this->_setView($view);
	}
}

class ControllerTest extends PHPUnit_Framework_TestCase {
	public function testTemplateRendersProperly() {
		$template = $this->getMock('Template', array('bind', 'render'));
		$view = $this->getMock('View');
		$template->expects($this->once())
			->method('bind')
			->with('VIEW', $view);
		$template->expects($this->once())
			->method('render');
		$controller = new ControllerTest_Controller($template, $view);
		$controller->render();
	}
}
?>
