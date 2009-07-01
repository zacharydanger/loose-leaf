<?php
require_once 'global.php';

class PasswordTokenTest extends PHPUnit_Framework_TestCase {
	public function badUserTypeProvider() {
		$types = array();
		$types[] = array(1337); //integers aren't right.
		$types[] = array(sha1(microtime(true))); //this really shouldn't be a class
		return $types;
	}

	/**
	 * @dataProvider badUserTypeProvider
	 * @expectedException Exception
	 */
	public function testSetBadUserType($user_type) {
		$US = new Password_Token();
		$US->user_type = $user_type;
	}

	public function goodUserTypeProvider() {
		$types = array();
		$types[] = array(User::TYPE_CUSTOMER);
		$types[] = array(User::TYPE_SALES);
		return $types;
	}

	/**
	 * @dataProvider goodUserTypeProvider
	 */
	public function testSetGoodUserType($user_type) {
		$US = new Password_Token();
		try {
			$US->user_type = $user_type;
			$this->assertEquals($user_type, $US->user_type);
		} catch(Exception $e) {
			$this->fail($e->getMessage());
		}
	}

	public function tokenFactoryProvider() {
		$args = array();
		$args[] = array('asdf', 'Customer', false);
		return $args;
	}

	/**
	 * @dataProvider tokenFactoryProvider
	 */
	public function testTokenFactory($token, $class_type, $exists) {
		$object = Password_Token::tokenFactory($token);
		$this->assertTrue(is_a($object, 'User'));
		$this->assertEquals($class_type, get_class($object));
		if(true == $exists) {
			$this->assertTrue($object->exists(), print_r($object,true));
		} else {
			$this->assertFalse($object->exists(), print_r($object,true));
		}
	}
}
?>