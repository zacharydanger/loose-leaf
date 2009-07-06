<?php
/**
 * This is a basic Active Record style class. Handles the CRUD aspects that are so tiring
 * and gives you access to getWhatever() and setWhatever($new_value).
 */
class Object {
	/**
	 * Unique ID for this object generally the primary key from the database.
	 */
	protected $_ID;

	/**
	 * Name of the table for this object in the database.
	 *
	 * Must be explicitly set in child classes.
	 */
	protected $_table = null;

	/**
	 * Name of the primary key of the table for this object in the database.
	 *
	 * Must be explicitly set in child classes.
	 */
	protected $_table_id = null;

	/**
	 * Array were we store our data from the DB.
	 */
	protected $_data = array();

	/**
	 * Default values for Object::$_data.
	 */
	protected $_default_vals = array();

	/**
	 * Fields that won't be sanitized. (Put fields that contain HTML in here.)
	 */
	protected $_unsanitized_fields = array();

	/**
	 * Hooks to run when setting a value.
	 */
	protected $_set_hooks = array();

	/**
	 * Hooks to run when getting a value.
	 */
	protected $_get_hooks = array();

	/**
	 * Validators to run when setting a value.
	 */
	protected $_validators = array();

	/**
	 * Unique hash for the instance of this Object with this ID.
	 */
	public $hash;

	/**
	 * Override the set method.
	 *
	 * @param name Name of the field to set.
	 * @param value New value..
	 * @return Returns true if the value was successfully set, false otherwise.
	 */
	public function __set($name, $value) {
		$value_set = false;
		if(true == array_key_exists($name, $this->_data)) {
			if(true == $this->_runValidators($name, $value)) {
				if(false == in_array($name, $this->_unsanitized_fields)) {
					$value = sanitize_string($value);
				}
				$value = $this->_runSetHooks($name, $value);
				$this->_data[$name] = $value;
				$value_set = true;
			}
		}
		return $value_set;
	}

	/**
	 * Runs validator functions defined in $this->_validator and returns true if they pass,
	 * false if any one of them fails.
	 *
	 * @param name Name of the field to run validators against.
	 * @param value Value to check if valid.
	 * @return Returns true if $value is a valid value for key $name, otherwise false.
	 */
	private function _runValidators($name, $value) {
		$valid = true;
		if(true == array_key_exists($name, $this->_validators)) {
			if(true == is_array($this->_validators[$name])) {
				foreach($this->_validators[$name] as $validator) {
					if(false == $this->$validator($value)) {
						$valid = false;
					}
				}
			} else {
				$validator = $this->_validators[$name];
				$valid = $this->$validator($value);
			}
		}
		return $valid;
	}

	/**
	 * Returns the value of after processing through any hooks from the
	 * $_set_hooks array.
	 *
	 * @param name Name of the field to run the hooks for.
	 * @param value New value that we'd like to set name to.
	 * @return Returns the potentially modified value after the set hooks have been run.
	 */
	private function _runSetHooks($name, $value) {
		return $this->_runHooks($name, $value, $this->_set_hooks);
	}

	/**
	 * Returns the value of after processing through any hooks from the
	 * $_get_hooks array.
	 *
	 * @param name Name of key to get the "get hooked" value from.
	 * @return Returns the value of $name after it's been run through all appropriate "get hooks".
	 */
	private function _runGetHooks($name) {
		$value = $this->_data[$name];
		return $this->_runHooks($name, $value, $this->_get_hooks);
	}

	/**
	 * Runs any hook found in $hook_array for field $name on $value
	 * @param name Name of the data field.
	 * @param value Value of the field for hook purposes.
	 * @param hook_array Array of hooks to run against the $name/$value pair.
	 * @return Returns the value of $name after it's been run through the hooks in $hook_array.
	 */
	private function _runHooks($name, $value, $hook_array) {
		$value = $value;
		if(true == array_key_exists($name, $hook_array)) {
			if(true == is_array($hook_array[$name])) {
				foreach($hook_array[$name] as $hook) {
					$value = $this->$hook($value);
				}
			} else {
				$hook = $hook_array[$name];
				$value = $this->$hook($value);
			}
		}
		return $value;
	}

	/**
	 * Override the get method.
	 *
	 * @param name Name of the field to return.
	 * @return Value of field with name `name`
	 */
	public function __get($name) {
		$value = null;
		if(true == array_key_exists($name, $this->_data)) {
			$value = $this->_runGetHooks($name, $this->_data[$name]);
			if(false == in_array($name, $this->_unsanitized_fields)) {
				$value = sanitize_string($value);
			}
		} elseif('ID' == $name) {
			$value = intval($this->_ID);
		}
		return $value;
	}

	/**
	 * Returns the unique ID of this object.
	 *
	 * @return Returns the unique ID for this Object.
	 */
	public function getID() {
		return $this->ID;
	}

	/**
	 * Takes a key to lookup and a key field to load up the object with. Defaults to the given
	 * table_id (primary key).
	 *
	 * @param ID Unique value that identifieds this Object.
	 * @param field The field to look for ID in. If null, defaults to the value of Object::$_table_id
	 */
	public function __construct($ID = 0, $field = null) {
		if(true == is_null($field)) {
			$field = $this->_table_id;
		}
		if(false == is_null($ID)) {
			$this->_load($ID, $field);
		} else {
			$this->_loadTable();
		}
		if(true == $this->exists()) {
			$this->hash = sha1($this->ID);
		} else {
			$this->hash = md5(uniqid(rand(), true));
		}

		Object_Factory::OF()->addObject($this);
	}

	/**
	 * Returns true if the object "exists" (has an ID > 0).
	 *
	 * @return Returns true if the Object exists (in the database), false otherwise.
	 */
	public function exists() {
		$exists = false;
		if(intval($this->_ID) > 0) {
			$exists = true;
		}
		return $exists;
	}

	/**
	 * Dumps the data array.
	 *
	 * @return Array of key => value pairs of the data for this Object.
	 */
	public function dataDump() {
		$data = array();
		$data[$this->_table_id] = intval($this->_ID);
		foreach($this->_data as $key => $value) {
			$data[$key] = stripslashes($value);
			if(false == in_array($key, $this->_unsanitized_fields)) {
				$data[$key] == sanitize_string(stripslashes($value));
			}
		}
		return $data;
	}

	/**
	 * This is where the getFoo() / setBar($bar) magic is handled.
	 *
	 * TODO: deprecate this function.
	 *
	 * @param name Name of the function being called.
	 * @param args Array of arguments passed to the fictitious function.
	 *
	 * @return Depending upon what the value of $name was, it either returns null, or the value of a given value.
	 */
	public function __call($name, $args = null) {
		if(false == is_array($args)) {
			//this is so we can call parent::__call('setWhatevs', $value) without wrapping $value in an array
			$args = array($args);
		}
		$first_three = substr($name, 0, 3);
		if('get' == $first_three || 'set' == $first_three) {
			$data_key = $this->_methodToKey($name);
			if('get' == $first_three) {
				return $this->$data_key;
			} elseif(true == is_array($args) && 1 == count($args)) {
				$this->$data_key = $args[0];
			}
		}
	}

	/**
	 * Takes a given method name and converts it to a data key.
	 *
	 * example: _methodToKey('getFooBar') returns 'foo_bar'
	 *
	 * @param method Method name we need to convert into a data key.
	 * @return Returns the data key.
	 */
	private function _methodToKey($method) {
		$method = substr($method, 3, (strlen($method) - 3));
		$upper = range('A', 'Z');

		$new_string = "";
		$method_length = strlen($method);
		$upper_switch = in_array($method[$i], $upper);
		for( $i = 0 ; $i < $method_length ; $i++) {
			if(false == $upper_switch && true == in_array($method[$i], $upper)) {
				$new_string .= "_";
			}
			if(false == $numeric_switch && true == is_numeric($method[$i])) {
				$new_string .= "_";
			}

			$new_string .= strtolower($method[$i]);
			$upper_switch = in_array($method[$i], $upper);
			$numeric_switch = is_numeric($method[$i]);
		}
		return $new_string;
	}

	/**
	 * Tries to load up a distinct object given a field / field value.
	 *
	 * @param ID Unique ID to lookup a record by.
	 * @param field Optional field name to do the lookup by, if null, will use the value from $this->_table_id.
	 */
	protected function _load($ID, $field = null) {
		if(true == is_null($field)) {
			$field = $this->_table_id;
		}
		if(false == is_null($this->_table) && false == is_null($this->_table_id)) {
			$sql = "SELECT *
				FROM `" . $this->_table . "` t
				WHERE t." . db_input($field) . " = '" . db_input($ID) . "'";
			$query = db_query($sql);
			while(1 == $query->num_rows && $row = $query->fetch_assoc()) {
				$this->_ID = $row[$this->_table_id];
				foreach($row as $key => $value) {
					if($key !== $this->_table_id) {
						$key = $key;
						$this->_data[$key] = $value;
					}
				}
			}

			if(0 == $this->_ID) { //didn't find anything? prefill our data array based on the table structure.
				$this->_loadTable();
			}
		}
	}

	/**
	 * Returns an array of objects based on a field / value. Makes it easy to lookup stuff.
	 *
	 * @param field Field (database column) to look up by. (Ex. parent_id)
	 * @param value Value of the field for which we are looking. (Ex. 1337)
	 * @param order_by_field Field to sort results by.
	 * @param order_direction Sort direction ASC/DESC
	 * @return Array of objects matching the criteria.
	 */
	public function find($field, $value, $order_by_field = null, $order_direction = 'ASC') {
		if(true == is_subclass_of($this, 'Object')) {
			$order_direction_list = array('DESC', 'ASC');
			$order_direction = trim(strtoupper($order_direction));
			if(false == in_array($order_direction, $order_direction_list)) {
				$order_direction = 'ASC';
			}

			$sql = "WHERE t.`" . db_input($field) . "` = '" . db_input($value) . "'" ;

			if(false == is_null($order_by_field)) {
				$sql .= " ORDER BY t.`" . db_input($order_by_field) . "` " . $order_direction;
			}
			return $this->findWhere($sql);
		}
	}

	/**
	 * Takes a literal SQL WHERE clause and returns an array of objects
	 * meeting that WHERE clause.
	 *
	 * TODO: Update this to maybe take parameterized options in an array maybe?
	 *
	 * @param where String that is a literal SQL "WHERE" clause.
	 * @return Array of Objects based on the results of the query.
	 */
	public function findWhere($where) {
		if(true == is_subclass_of($this, 'Object')) {
			$class = get_class($this);

			$sql = "SELECT `" . db_input($this->_table_id) . "`
				FROM `" . db_input($this->_table) . "` t " . $where;
			$object_array = array();
			$query = db_query($sql);
			while($query->num_rows > 0 && $o = $query->fetch_assoc()) {
				$object_array[] = Object_Factory::OF()->newObject($class, $o[$this->_table_id]);
			}
			return $object_array;
		}
	}

	/**
	 * Loads up the data array for a given class if no record can be found.
	 */
	protected function _loadTable() {
		$sql = "DESCRIBE `" . $this->_table . "`";
		$query = db_query($sql);
		while($query->num_rows > 0 && $rec = $query->fetch_assoc()) {
			if($rec['Field'] != $this->_table_id) {
				$this->_data[$rec['Field']] = exists($rec['Field'], $this->_default_vals, null);
			}
		}
	}

	/**
	 * Writes the object to the table. Handles both SQL INSERT/UPDATE functionality without having to call them
	 * explicitly.
	 */
	public function write() {
		if(intval($this->_ID) > 0) {
			$this->_update();
		} else {
			$this->_insert();
		}
	}

	/**
	 * Updates the record in the database.
	 */
	protected function _update() {
		if(intval($this->_ID) > 0 && false == is_null($this->_table) && false == is_null($this->_table_id)) {
			$data = $this->_makeRecord();
			$where = "WHERE " . $this->_table_id . " = '" . intval($this->_ID) . "'";
			db_perform($this->_table, $data, SQL_UPDATE, $where);
		}
	}

	/**
	 * Inserts a new record in teh database.
	 */
	protected function _insert() {
		if(false == is_null($this->_table) && false == is_null($this->_table_id)) {
			$data = $this->_makeRecord();
			db_perform($this->_table, $data, SQL_INSERT);
			$this->_ID = db_insert_id();
			Object_Factory::OF()->updateMap($this);
		}
	}

	/**
	 * This function creates an array suitable for use with db_perform.
	 *
	 * @return A key => value array suitable for use with db_perform().
	 */
	protected function _makeRecord() {
		$data = array();
		foreach($this->_data as $key => $value) {
			$data[$key] = $value;
		}
		return $data;
	}

	/**
	 * Delete's a the object from the table. Can be overridden if need be to do extra processing
	 * before / after a delete.
	 */
	public function delete() {
		if(intval($this->_ID) > 0) {
			$sql = "DELETE FROM `" . db_input($this->_table) . "`
				WHERE `" . db_input($this->_table_id) . "` = '" . intval($this->_ID) . "'";
			db_query($sql);
			foreach($this->_data as $key => $value) {
				$this->_data[$key] = null;
			}
			$this->_ID = 0;
		}
	}

	/**
	 * Returns all the data keys. Great for determining table structure.
	 *
	 * @return Returns an array of data keys.
	 */
	public function listKeys() {
		$keys = array();
		foreach($this->_data as $key => $value) {
			$keys[] = $key;
		}
		return $keys;
	}

	/**
	 * Loads the protected $_data array with the values from $new_data
	 *
	 * @param new_data key => value array for bulk loading new data into the object.
	 */
	public function load(array $new_data) {
		foreach($new_data as $key => $value) {
			if(true == array_key_exists($key, $this->_data)) {
				$this->$key = $value;
			}
		}
	}
}
?>