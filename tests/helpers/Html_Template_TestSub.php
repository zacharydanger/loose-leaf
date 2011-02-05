<?php
require_once dirname(__FILE__) . '/../../Html_Template.php';

/**
 * Test specific subclass for getting at various private vars.
 */
class Html_Template_TestSub extends LooseLeaf\Html_Template {

	/**
	 * Override since we don't care about an actual file.
	 */
	public function __construct($file = null) {
		/* do nothing */
	}

	/**
	 * Returns the bound variables.
	 */
	public function getVars() {
		return $this->_vars;
	}
}
?>
