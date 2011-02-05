<?php
require_once __DIR__ . '/../Router.php';
require_once __DIR__ . '/../Controller.php';

use LooseLeaf\Router as Router;
use LooseLeaf\Controller as Controller;

class Foobar_Controller extends Controller { /* stub for testing */ }

class RouterTest extends \PHPUnit_Framework_TestCase {
	public function testAdd() {
		$alias = '/home/foobar/foo/asdf/jklmnop/';
		$new_route = array('controller' => 'Foobar_Controller', 'action' => 'foooobar', 'id' => 'eye-dee');
		Router::add($alias, $new_route);
		$route = Router::route($alias);
		foreach($route as $key => $value) {
			$this->assertEquals($new_route[$key], $value);
		}
	}

	public function testTrailingSlashRoute() {
		$alias = '/home/foobar/foo/asdf/jklmnop';
		$new_route = array('controller' => 'Foobar_Controller', 'action' => 'foooobar', 'id' => 'eye-dee');
		Router::add($alias . '/', $new_route);
		$route = Router::route($alias);
		foreach($route as $key => $value) {
			$this->assertEquals($new_route[$key], $value);
		}
	}

	public function testControllerAlias() {
		$original_controller = sha1(microtime(true));
		$alias = sha1($original_controller);
		Router::aliasController($alias, $original_controller);
		$route = Router::route($alias);
		$this->assertEquals($original_controller, $route['controller']);
	}

	public function testActionAlias() {
		$controller = 'Foobar_Controller';
		$alias = 'short'; //see how short it is? nice, right?
		$original_action = 'orignalActionNineteenFortyTwo'; //it's long and encumbered.
		Router::aliasAction($controller, $alias, $original_action);
		$route = Router::route('foobar/short/');
		$this->assertEquals($original_action, $route['action']);
	}

	public function testBadController() {
		$url = '/asdf/jkl/qwerty/';
		$route = Router::route($url);
		$this->assertFalse($route['controller']);
	}
}
?>
