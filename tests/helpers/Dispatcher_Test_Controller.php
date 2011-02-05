<?php
require_once dirname(__FILE__) . '/../../Controller.php';

class Dispatcher_Test_Controller extends LooseLeaf\Controller {
	public function foobar() {
		/* test stub, do nothing */
	}

	public function render() {
		return null;
	}

	public function returnRenderable() {
		/* nada */
	}
}
