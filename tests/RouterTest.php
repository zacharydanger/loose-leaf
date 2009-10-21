<?php
require_once '../lib/functions/standard_lib.php';
require_once '../lib/classes/Router.php';
require_once '../lib/classes/Controller.php';

/**
 * Stub class for testing the Router.
 */
class Foobar_Controller extends Controller { }

class RouterTest extends PHPUnit_Framework_TestCase {
	public function testUrlRoute() {
		Router::add('/', array('controller' => 'Foobar_Controller', 'action' => 'asdf'));
		$route = Router::route('/');
		$this->assertEquals('Foobar_Controller', $route['controller']);
		$this->assertEquals('asdf', $route['action']);
	}

	public function testNonAliasedRoute() {
		$url = 'foobar/foo/1';
		$route = Router::route($url);
		$this->assertEquals('Foobar_Controller', $route['controller']);
		$this->assertEquals('foo', $route['action']);
		$this->assertEquals('1', $route['id']);
	}
}