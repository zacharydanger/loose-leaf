<?php
require_once __DIR__ . '/../../Controller.php';
require_once __DIR__ . '/../../Redirect_Request.php';

/**
 * Subclass for testing the Controller class.
 */
class Controller_TestSub extends Controller {
	const LOGIN_REQUIRE_URL = '/foobar.php';

	public function subRedirect($location) {
		$this->redirect($location);
	}

	public function redirRequest($location) {
		return new Redirect_Request($location);
	}

	public static function returnSomething() {
		return 'something';
	}

	public static function returnRenderable() {
	}

	public function requireLogin() {
		parent::requireLogin(self::LOGIN_REQUIRE_URL);
	}
}
?>
