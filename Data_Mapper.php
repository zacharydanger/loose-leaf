<?php
namespace LooseLeaf;

require_once __DIR__ . '/exceptions/Data_Mapper_Exception.php';

class Data_Mapper {
	protected $connection;

	protected $record_class;

	protected $table;

	protected $class_map = array();

	protected $primary_key;

	public function __construct($connection = null) {
		$this->connection = $connection;
	}

	public function get($id) {
		$query = sprintf("select %s from `%s` where %s", $this->getFields(), $this->getTable(), $this->getWhere());
		$statement = $this->connection->prepare($query);
		$statement->bindValue('key', $id);

		$values = array();
		$class_name = $this->record_class;
		$class = new $class_name();
		if(true == $statement->execute()) {
			foreach($statement->fetchAll() as $row) {
				foreach($row as $field => $value) {
					if(array_key_exists($field, $this->class_map)) {
						$object_field = $this->class_map[$field];
						$class->$object_field = $value;

						if($field == $this->primary_key) {
							$class->ID = intval($value);
						}
					}
				}
			}
		}
		return $class;
	}

	public function delete(\LooseLeaf\Model $model) {
		if(intval($model->ID) == 0) {
			return null;
		}

		$sql = sprintf("DELETE FROM `%s` WHERE %s",
			$this->getTable(),
			$this->getWhere()
		);

		$statement = $this->connection->prepare($sql);

		$params = array(':key' => $model->ID);
		if(false == $statement->execute($params)) {
			throw new Data_Mapper_Exception("Something went wrong with the deletion.");
		}

		$model->ID = 0;

		$primary_field = $this->class_map[$this->primary_key];
		$model->$primary_field = null;
	}

	public function save(\LooseLeaf\Model $model) {
		if(intval($model->ID) > 0) {
			$this->update($model);
		} else {
			$this->insert($model);
		}
	}

	private function update($model) {
		$sql = sprintf("UPDATE `%s` SET %s WHERE %s",
			$this->getTable(),
			$this->getUpdateSet($model),
			$this->getWhere()
		);

		$statement = $this->connection->prepare($sql);

		$params = array(':key' => $model->ID);

		foreach($this->class_map as $db_field => $object_field) {
			if($db_field != $this->primary_key) {
				$params[':' . $db_field] = $model->$object_field;
			}
		}

		if(false == $statement->execute($params)) {
			throw new Data_Mapper_Exception("Something went wrong with the update.");
		}
	}

	private function getUpdateSet(\LooseLeaf\Model $model) {
		$set_list = array(); //I hope they play Free Bird
		foreach($this->class_map as $db_field => $object_field) {
			if($db_field != $this->primary_key) {
				$set_list[] = sprintf('`%s` = :%s', $db_field, $db_field);
			}
		}

		return implode(', ', $set_list);
	}

	private function insert($model) {
		$fields = array();
		$values = array();
		$params = array();
		foreach($this->class_map as $db_field => $object_field) {
			if($db_field != $this->primary_key) {
				$fields[] = "`" . $db_field . "`";
				$values[] = ":" . $db_field;
				$params[":" . $db_field] = $model->$object_field;
			}

		}

		$sql = sprintf("INSERT INTO `%s` (%s) VALUES (%s)",
			$this->getTable(),
			implode(', ', $fields),
			implode(', ', $values)
		);

		$statement = $this->connection->prepare($sql);

		if(false == $statement->execute($params)) {
			throw new Data_Mapper_Exception("Problem inserting record.");
		}

		$model->ID = intval($this->connection->lastInsertId());
	}

	protected function getFields() {
		$fields = array("`" . $this->primary_key . "`");

		foreach($this->class_map as $db_field => $object_field) {
			$field = '`' . $db_field . '`';
			if(!in_array($field, $fields)) {
				$fields[] = $field;
			}
		}

		return implode(', ', $fields);
	}

	protected function getTable() {
		return $this->table;
	}

	protected function getWhere() {
		return sprintf("`%s` = :key", $this->primary_key);
	}
}