<?php
abstract class Html_Template {
	protected $_file;
	protected $_vars = array();

	public function __construct($file_name) {
		if(false == file_exists($file_name)) {
			throw new Exception("View doesn't exist.");
		}
		$this->_file = $file_name;
	}

	public function bind($var_name, $var) {
		$this->_vars[$var_name] = $var;
	}

	public function render() {
		foreach($this->_vars as $var_name => $value) {
			$$var_name = $value;
		}
		require $this->_file;
	}
}
?>
