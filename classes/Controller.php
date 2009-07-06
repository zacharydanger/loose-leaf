<?php
abstract class Controller { 
	protected $_template;
	protected $_view;
	protected $_user;

	protected function _setTemplate(Template $template) {
		$this->_template = $template;
	}

	protected function _setView(View $view) {
		$this->_view = $view;
	}

	public function render() {
		$template = new Template($this->_template);
		$template->bind('VIEW', $this->_view);
		$template->render();	
	}

	public function setUser(User $user) {
		$this->_user = $user;
	}
}
?>