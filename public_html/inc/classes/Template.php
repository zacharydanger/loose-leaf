<?php
require_once 'Html_Template.php';

class Template extends Html_Template {
	public function __construct($file_name) {
		$file_name = 'inc/templates/' . $file_name;
		parent::__construct($file_name);
	}
}
?>
