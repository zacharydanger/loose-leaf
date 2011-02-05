<?php
class Html_Template {
	protected $_file;
	protected $_vars = array();

	public function __construct($file_name) {
		if(false == file_exists($file_name)) {
			throw new Exception("View '" . $file_name . "' doesn't exist.");
		}
		$this->_file = $file_name;
	}

	public function bind($var_name, $var) {
		$this->_vars[$var_name] = $var;
	}

	public function render() {
		extract($this->_vars);
		require $this->_file;
	}

	public function returnOutput() {
		ob_start();
		$this->render();
		$output = ob_get_clean();
		return $output;
	}

	public function getFile() {
		return $this->_file;
	}

	public function getBindings() {
		return $this->_vars;
	}

	public function getBinding($key) {
		$val = null;
		if(true == array_key_exists($key, $this->_vars)) {
			$val = $this->_vars[$key];
		}
		return $val;
	}
}
?>
