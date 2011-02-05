<?php
namespace LooseLeaf;

require_once dirname(__FILE__) . '/exceptions/Dispatcher_Permission_Exception.php';
require_once dirname(__FILE__) . '/exceptions/Controller_Not_Found_Exception.php';
require_once dirname(__FILE__) . '/Redirector.php';
require_once dirname(__FILE__) . '/Controller_Locator.php';

/**
 * The Dispatcher class dispatches actions to controllers, etc.
 */
class Dispatcher {
	private $_controller;
	private $_action;
	private $_id;
	private $_user;

	public function __construct($controller, $action = null, $id = null) {
		$this->_setController($controller);
		$this->_setAction($action);
		$this->_setId($id);
	}

	private function _setController($controller) {
		if(true == class_exists($controller)) {
			$this->_controller = $controller;
		} else {
			throw new Controller_Not_Found_Exception("Controller '$controller' doesn't exist!?");
		}
	}

	private function _setAction($action) {
		$this->_action = 'index';
		if(true == isset($this->_controller)) {
			if(false == is_null($action) && false == empty($action)) {
				if(true == method_exists($this->_controller, $action)) {
					$this->_action = $action;
				}
			}
		}
	}

	private function _setId($id) {
		if(true == isset($this->_controller) && true == isset($this->_action)) {
			if(false == is_null($id) && false == empty($id)) {
				$this->_id = $id;
			}
		}
	}

	public function dispatch() {
		if(true == isset($this->_controller) && true == isset($this->_action)) {
			$class_name = $this->_controller;
			$C = Controller_Locator::get()->findController($class_name);
			$action_name = $this->_action;
			try {
				$output = $C->$action_name($this->_id);
				if(true == ($output instanceof \Redirectable)) {
					Redirector::get()->redirect($output->getLocation());
				} elseif($output instanceof \Renderable) {
					$output->render();
				} elseif(false == is_null($output)) {
					echo $output;
				}
			} catch(Redirect_Exception $e) {
				Redirector::get()->redirect($e->getLocation());
			}
		}
	}
}