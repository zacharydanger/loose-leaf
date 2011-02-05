<?php
/**
 * Singleton class that will build any object that extends the Object class. What's more is it keeps track of what
 * Objects have been instantiated and returns them if they already exist.
 */
class Object_Factory {
	private static $_instance;
	private $_object_list = array();
	private $_id_map = array();

	/**
	 * Private constructor, the essence of the singleton.
	 */
	private function __construct() { }

	/**
	 * Returns the static instance of the Object_Factory singleton.
	 */
	public static function OF() {
		if(false == isset(self::$_instance)) {
			$c = __CLASS__;
			self::$_instance = new $c();
		}
		return self::$_instance;
	}

	/**
	 * Catches calls to 'newMethodName(ID)' calls.
	 */
	public function __call($name, $args = null) {
		if(false == is_array($args)) {
			//this is so we can call parent::__call('setWhatevs', $value) without wrapping $value in an array
			$args = array($args);
		}

		$first_three = substr($name, 0, 3);
		$class_name = $this->_methodToClass($name);

		if('new' == $first_three && true == class_exists($class_name) && 'Object' == get_parent_class($class_name)) {
			$ID = intval($args[0]);
			return $this->newObject($class_name, $ID);
		}
	}

	/**
	 * Lets you explicitly build a class.
	 */
	public function newObject($class, $ID) {
		$ID = abs(intval($ID));
		$hash = sha1($ID);
		if(true == array_key_exists($class, $this->_id_map) && true == array_key_exists($ID, $this->_id_map[$class])) {
			$hash = $this->_id_map[$class][$ID];
		}
		if((true == is_array($this->_object_list[$class]) && false == array_key_exists($hash, $this->_object_list[$class])) || false == is_array($this->_object_list[$class])) {
			$object = new $class($ID);
		} else {
			$object = $this->_object_list[$class][$hash];
		}

		return $object;
	}

	public function destroy($object) {
		if(true == is_a($object, 'Object') && true == $object->exists()) {
			if(true == isset($this->_object_list[get_class($object)][$object->hash])) {
				unset($this->_object_list[get_class($object)][$object->hash]);
			}
		}
	}

	public function addObject($object) {
		if(true == is_a($object, 'Object')) {
			if(false == is_array($this->_object_list[get_class($object)]) || false == array_key_exists($object->ID, $this->_object_list[get_class($object)])) {
				$this->_object_list[get_class($object)][$object->hash] = $object;
				if(true == $object->exists()) {
					$this->_id_map[get_class($object)][$object->ID] = $object->hash;
				}
			}
		}
	}

	public function updateMap(Object $object) {
		$this->addObject($object);
	}

	/**
	 * Converts a method name to a class name.
	 */
	private function _methodToClass($method) {
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

			$new_string .= $method[$i];
			$upper_switch = in_array($method[$i], $upper);
			$numeric_switch = is_numeric($method[$i]);
		}
		return $new_string;
	}
}
?>