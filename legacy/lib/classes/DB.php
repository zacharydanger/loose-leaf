<?php
/**
 * This is a singleton class that holds the default data connection for the application. This link will be used in any of the
 * functions in inc/functions/db.php
 */
class DB {
	private static $_instance;
	private $_link;

	private function __construct($host, $user, $pass, $name) {
		@$this->_link = new mysqli($host, $user, $pass, $name);
		if(mysqli_connect_errno()) {
			throw new Exception("Whoops, couldn't connect to our database. MySQL Said: " . mysqli_connect_error());
		}
	}

	public static function DB($host = DB_HOST, $user = DB_USER, $pass = DB_PASS, $name = DB_NAME) {
		if(false == isset(self::$_instance)) {
			$c = __CLASS__;
			self::$_instance = new $c($host, $user, $pass, $name);
		}
		return self::$_instance;
	}

	/**
	 * Guess what you shouldn't do? Run this method. Ever. It's only used for unit testing.
	 */
	public static function unsetDB() {
		self::$_instance = null;
	}

	public function getLink() {
		return $this->_link;
	}

	public function setLink(mysqli $link) {
		$this->_link = $link;
	}
}
?>