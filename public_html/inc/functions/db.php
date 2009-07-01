<?php
/* These are some crappy database functions. :] */
define('SQL_INSERT', 1, false);
define('SQL_UPDATE', 2, false);

/**
 * Returns the database error number.
 */
function db_errno() {
	$DB = DB::DB()->getLink();
	return $DB->errno;
}

/**
 * Returns the error message from the database.
 */
function db_error() {
	$DB = DB::DB()->getLink();
	return $DB->error;
}

/**
 * Inserts or Updates an array of data into a table.
 */
function db_perform($table_name, $data = array(), $sql_action = SQL_INSERT, $where = '') {
	$sql = null;
	switch ($sql_action) {
		case SQL_INSERT: {
			$field_list = array();
			$value_list = array();
			foreach($data as $field => $value) {
				$field_list[] = "`" . $field . "`";
				$value_list[] = "'" . db_input($value) . "'";
			}
			$field_string = implode(',', $field_list);
			$value_string = implode(',', $value_list);

			$sql = "INSERT INTO `" . db_input($table_name) . "` (" . $field_string . ") VALUES (" . $value_string . ")";
			break;
		}

		case SQL_UPDATE: {
			$sql = "UPDATE `" . db_input($table_name) . "` SET ";
			$set_list = array();
			foreach($data as $field => $value) {
				$set_list[] = "`" . $field . "`" . " = '" . db_input($value) . "'";
			}
			$sql .= implode(', ', $set_list);
			$sql .= " " . $where;
			break;
		}
	}

	if(false == is_null($sql)) {
		return db_query($sql);
	}
}

/**
 * Queries the database.
 */
function db_query($sql) {
	global $QUERY_COUNT, $QUERY_LIST;
	$DB = DB::DB()->getLink();
	if(true == empty($sql)) {
		$backtrace = debug_backtrace();
		$warning = "<br />Empty Query in <strong>" . $backtrace[0]['file'] . "</strong> on line <strong>" . $backtrace[0]['line'] . "</strong><br />" . $sql;
		trigger_error($warning, E_USER_ERROR);
	}
	if(true == defined('DEBUG_MODE') && true == DEBUG_MODE) {
		$QUERY_COUNT++;
		$QUERY_LIST[] = "\n\n" . str_replace("\t", "", $sql) . "\n";
	}
	$query = $DB->query($sql);
	if(db_errno() > 0) {
		$backtrace = debug_backtrace();
		$warning = "<br />MySQL Error in <strong>" . $backtrace[0]['file'] . "</strong> on line <strong>" . $backtrace[0]['line'] . "</strong>, ";
		$warning .= "<blockquote><strong>MySQL Says:</strong> <em>" . db_error() . "</em></blockquote>" . $sql;
		trigger_error($warning, E_USER_ERROR);
	}
	return $query;
}

function db_multi_query($sql) {
	global $QUERY_COUNT, $QUERY_LIST;
	$DB = DB::DB()->getLink();
	$query = $DB->multi_query($sql);
	if(db_errno() > 0) {
		$backtrace = debug_backtrace();
		$warning = "<br />MySQL Error in <strong>" . $backtrace[0]['file'] . "</strong> on line <strong>" . $backtrace[0]['line'] . "</strong>, ";
		$warning .= "<blockquote><strong>MySQL Says:</strong> <em>" . db_error() . "</em></blockquote>" . $sql;
		trigger_error($warning, E_USER_ERROR);
	}
	return $query;
}

/**
 * Returns a random result set from a given query.
 */
function db_query_random($sql, $upper_bound = 10) {
	$query = db_query($sql);
	$upper_bound = ($query->num_rows > $upper_bound) ? $upper_bound : $query->num_rows;
	$used_indices = array();
	$results = array();
	while(count($results) < $upper_bound) {
		$index = rand(0, $query->num_rows - 1);
		if(false == in_array($index, $used_indices)) {
			$query->data_seek($index);
			$results[] = $query->fetch_assoc();
			$used_indices[] = $index;
		}
	}
	return $results;
}

/**
 * Cleans up a string for database entry.
 */
function db_input($string) {
	$DB = DB::DB()->getLink();
	return $DB->real_escape_string($string);
}

/**
 * Gets the latest key written to the database.
 */
function db_insert_id() {
	$DB = DB::DB()->getLink();
	return $DB->insert_id;
}

/**
 * Clone a mysql database from $source to $target.
 *
 * NOTE: The mysqli link form DB::getLink() needs to be for a user with privileges for both
 * databases.
 */
function db_clone($source, $target, $debug = false) {
	$source = db_input($source);
	$target = db_input($target);

	if(true == $debug) {
		echo "Cloning `$source` to `$target`";
	}

	$sql = "SHOW TABLES FROM `" . $source . "`";
	$tables_query = db_query($sql);
	while($tables_query->num_rows > 0 && $t = $tables_query->fetch_array()) {
		if(true == $debug) {
			echo ".";
		}
		$table = $t[0];
		$sql = "DROP TABLE IF EXISTS `" . $target . "`.`" . $table . "`";
		db_query($sql);
		$sql = "CREATE TABLE `" . $target . "`.`" . $table . "`
				LIKE `" . $source . "`.`" . $table . "`";
		db_query($sql);
		$sql = "INSERT INTO `" . $target . "`.`" . $table . "`
				SELECT *
				FROM `" . $source . "`.`" . $table . "`";
		db_query($sql);
	}
	if(true == $debug) {
		echo "done!\n";
	}
}
?>