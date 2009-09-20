<?php
class SQL_Update extends SQL_Statement {
	private $_table;
	private $_set_list = array();

	public function __construct($table) {
		$this->_table = $table;
	}

	public function set($field, $value) {
		$this->_set_list[$field] = $value;
		return $this;
	}

	public function getSql() {
		$sql = "UPDATE `" . trim(db_input($this->_table)) . "` SET ";
		$set_list = array();
		foreach($this->_set_list as $field => $value) {
			$set_list[] = " `" . $field . "` = '" . db_input($value) . "' ";
		}
		$sql .= implode(', ', $set_list);
		if(count($this->_where_clause_list) > 0) {
			$sql .= " WHERE ";
			$sql .= implode(' AND ', $this->_where_clause_list);
		}

		return $sql;

	}
}
?>
