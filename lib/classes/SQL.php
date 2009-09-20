<?php
class SQL {
	/**
	 * Easy way to get a new instance statically.
	 */
	public static function get() {
		$class_name = __CLASS__;
		return new $class_name();
	}

	/**
	 * Builds a new SQL_Select object with any given args and returns it.
	 */
	public function select() {
		return new SQL_Select(func_get_args());
	}

	/**
	 * Builds a new SQL_Update object and returns it.
	 */
	public function update($table) {
		return new SQL_Update($table);
	}
}
?>
