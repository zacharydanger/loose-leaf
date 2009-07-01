<?php
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
			throw new Exception("Controller doesn't exist!?");
		}
	}

	private function _setAction($action) {
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
			$C = new $class_name();
			if(isset($this->_user)) {
				$C->setUser($this->_user);
			}
			$action_name = $this->_action;
			$C->$action_name($this->_id);
			$C->render();
		}
	}

	public function setUser(User $user) {
		$this->_user = $user;
	}
}
?>