<?php
require_once 'global.php';

class UserSessionTest extends PHPUnit_Framework_TestCase {
	protected $fixture = array();

	public function setUp() {
		$token = sha1(microtime());
		$data = array('user_id' => 1337, 'user_type' => 'bad_user_type', 'token' => $token);
		db_perform('user_sessions', $data, SQL_INSERT);
		$this->fixture['exception_token'] = $token;
	}

	public function tearDown() {
		$sql = "DELETE FROM user_sessions
			  WHERE token = '" . $this->fixture['exception_token'] . "'";
		db_query($sql);
	}

	public function tokenFactoryProvider() {
		$args = array();
		$args[] = array('asdf', 'Customer', false);

		$C = new Customer();
		$email = sha1(microtime() . rand(0,99)) . '@localhost.foo';
		$pass = sha1(microtime() . rand(0,99));
		$C->setEmail($email);
		$C->setPassword($pass);
		$C->newToken();
		$C->write();
		$C->login($email, $pass);
		$args[] = array($C->getToken(), 'Customer', true);

		$C = new Sales_Rep();
		$email = sha1(microtime() . rand(0,99)) . '@localhost.foo';
		$pass = sha1(microtime() . rand(0,99));
		$C->setEmail($email);
		$C->setPassword($pass);
		$C->newToken();
		$C->write();
		$C->login($email, $pass);
		$args[] = array($C->getToken(), 'Sales_Rep', true);

		return $args;
	}

	/**
	 * @dataProvider tokenFactoryProvider
	 */
	public function testTokenFactory($token, $class_type, $exists) {
		$object = User_Session::tokenFactory($token);
		$this->assertTrue(is_a($object, 'User'));
		$this->assertEquals($class_type, get_class($object));
		if(true == $exists) {
			$this->assertTrue($object->exists(), print_r($object,true));
		} else {
			$this->assertFalse($object->exists(), print_r($object,true));
		}
	}

	/**
	 * @expectedException Exception
	 */
	public function testTokenFactoryException() {
		$foo = User_Session::tokenFactory($this->fixture['exception_token']);
	}

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
		$US = new User_Session();
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
		$US = new User_Session();
		try {
			$US->user_type = $user_type;
			$this->assertEquals($user_type, $US->user_type);
		} catch(Exception $e) {
			$this->fail($e->getMessage());
		}
	}

	public function testGenerateToken() {
		$US = new User_Session();
		$old_tokens = array();
		for($i=0;$i<10;$i++) {
			$token = $US->generateToken();
			$this->assertEquals($token, $US->token);
			$this->assertFalse(in_array($US->token, $old_tokens));
		}
	}
}
?>