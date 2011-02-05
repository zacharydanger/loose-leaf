<?php
require_once __DIR__ . '/exceptions/Redirect_Exception.php';

abstract class Controller { 
	protected function redirect($location) {
		$exception = new Redirect_Exception("Something happened, please redirect.");
		$exception->setLocation($location);
		throw $exception;
	}
}