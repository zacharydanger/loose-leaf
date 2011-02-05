<?php
require_once __DIR__ . '/interfaces/Redirectable.php';

/**
 * Simple class that acts as a request to redirect.
 */
class Redirect_Request implements Redirectable {
	private $_location;

	public function __construct($location) {
		$this->_location = $location;
	}

	public function getLocation() {
		return $this->_location;
	}
}
?>
