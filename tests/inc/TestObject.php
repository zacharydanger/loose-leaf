<?php
/**
 * This is a class that can be used to thes the Object class.
 */
class TestObject extends Object {
	protected $_data = array(
			'foo' => 'bar',
			'bar' => 'foo',
			'asdf' => 'qwerty',
			'qwerty' => 'asdf');
	protected $_get_hooks = array(
			'foo' => 'fooGetHook',
			'bar' => array('barGetHook1', 'barGetHook2')
			);

	protected $_set_hooks = array(
			'foo' => 'fooSetHook',
			);

	protected $_validators = array(
			'asdf' => 'asdfValidator',
			'qwerty' => array('qwertyValidator1', 'qwertyValidator2')
			);

	protected function fooSetHook($value) {
		return strtoupper($value);
	}

	protected function fooGetHook($value) {
		return "#" . $value . "#";
	}

	protected function barGetHook1($value) {
		return "#" . $value . "#";
	}

	protected function barGetHook2($value) {
		return "@" . $value . "@";
	}

	protected function asdfValidator($value) {
		return ("qwerty" == $value);
	}

	protected function qwertyValidator1($value) {
		return true;
	}

	protected function qwertyValidator2($value) {
		return ("asdf" == $value);
	}
}

class Test_Object_Foo extends Object {
	protected $_data = array(
				'asdf' => 'asdf',
				'qwerty' => 'qwerty');
}
?>