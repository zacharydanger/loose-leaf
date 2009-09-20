<?php
class SQL_Update {
	private $_table;
	private $_where_clause_list = array();
	private $_set_list = array();
	private $_bound_variables = array();

	public function __construct($table) {
		$this->_table = $table;
	}

	public function set($field, $value) {
		$this->_set_list[$field] = $value;
		return $this;
	}

	public function where($where_clause) {
		$this->_where_clause_list[] = $where_clause;
		return $this;
	}
	
	public function bind($field_name, $value) {
		$this->_bound_variables[$field_name] = $value;
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

	public function __toString() {
		return $this->getSql();
	}
}
?>
