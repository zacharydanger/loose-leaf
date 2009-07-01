<?php
require_once 'Html_Template.php';

class View extends Html_Template {
	public function __construct($file_name) {
		$file_name = 'inc/views/' . $file_name;
		parent::__construct($file_name);
	}
}
?>
