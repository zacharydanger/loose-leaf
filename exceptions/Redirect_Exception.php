<?php
require_once __DIR__ . '/../interfaces/Redirectable.php';

/**
 * Class for redirecting.
 */
class Redirect_Exception extends Exception implements Redirectable {
	const DEFAULT_LOCATION = '/';

	private $_location;

	public function getLocation() {
		$location = self::DEFAULT_LOCATION;
		if(true == isset($this->_location)) {
			$location = $this->_location;
		}
		return $location;
	}

	public function setLocation($redirect_location) {
		$this->_location = $redirect_location;
	}
}
?>