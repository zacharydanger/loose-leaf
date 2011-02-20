<?php
namespace LooseLeaf;

class Model {
	public $ID = 0;

	protected $data = array();

	public function __set($name, $value) {
		if(array_key_exists($name, $this->data)) {
			$setter_function = '_set_' . $name;

			if(method_exists($this, $setter_function)) {
				$value = $this->$setter_function($value);
			}

			$this->data[$name] = $value;
		} else {
			trigger_error("Field $name does not exist on object.");
		}
	}

	public function __get($name) {
		$value = null;
		if(array_key_exists($name, $this->data)) {
			$value = $this->data[$name];

			$getter_function = '_get_' . $name;
			if(method_exists($this, $getter_function)) {
				$value = $this->$getter_function();
			}
		}
		return $value;
	}

	public function __isset($name) {
		return isset($this->data[$name]);
	}

	public function load(array $new_data) {
		foreach($new_data as $key => $value) {
			if(array_key_exists($key, $this->data)) {
				$this->$key = $value;
			}
		}
	}
}
