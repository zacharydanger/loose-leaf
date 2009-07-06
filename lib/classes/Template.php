<?php
require_once 'Html_Template.php';

class Template extends Html_Template {
	public function __construct($file_name) {
		$template_dir = LL_Config::$config['paths']['templates'];
		$file_name = $template_dir . $file_name;
		parent::__construct($file_name);
	}
}
?>
