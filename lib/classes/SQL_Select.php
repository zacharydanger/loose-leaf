<?php
class SQL_Select extends SQL_Statement {
	private $_selected_fields = array();
	private $_from_tables = array();
	private $_table_aliases = array();
	private $_left_joins = array();

	public function __construct($field_list = array()) {
		foreach($field_list as $field) {
			$this->select($field);
		}
	}

	public function select() {
		$fields = func_get_args();
		foreach($fields as $field) {
			$this->_selected_fields[] = trim($field);
		}
		return $this;
	}

	public function from($table, $alias = null) {
		$this->_from_tables[] = $table;
		if(false == is_null($alias)) {
			$this->_table_aliases[$table] = $alias;
		}
		return $this;
	}

	public function leftJoin($table, $field_1, $field_2) {
		$join_array = array(
				'table' => $table,
				'field_1' => $field_1,
				'field_2' => $field_2
			);
		$this->_left_joins[] = $join_array;
		return $this;
	}

	public function getSql() {
		$sql = null;
		if(count($this->_selected_fields) > 0) {
			$sql .= "SELECT ";

			$clean_fields = array();
			foreach($this->_selected_fields as $field) {
				$clean_fields[] = '`' . $field . '`';
			}
			$sql .= implode(',', $clean_fields);
			$sql .= "\n";
		}

		if(count($this->_from_tables) > 0) {
			$sql .= " FROM ";
			$table_list = array();
			foreach($this->_from_tables as $table) {
				$alias = exists($table, $this->_table_aliases, null);
				$table_list[] = $table . ' ' . $alias;	
			}
			$sql .= implode(', ', $table_list);
		}

		if(count($this->_left_joins) > 0) {
			foreach($this->_left_joins as $join_data) {
				$sql .= " LEFT JOIN " . $join_data['table'];
				$sql .= " ON " . $join_data['field_1'] . ' = ' 
					. $join_data['field_2']. " ";
			}
		}

		if(count($this->_where_clause_list) > 0) {
			$sql .= ' WHERE ' . implode(' AND ', $this->_where_clause_list);
		}

		foreach($this->_bound_variables as $var => $val) {
			$sql = str_replace('@'.$var, $val, $sql);
		}
		return $sql;
	}
}
?>
