<?php
require_once 'global.php';

class MessageStackTest extends PHPUnit_Framework_TestCase {
	public function testAddMessages() {
		$_SESSION = array();
		$MS = new Message_Stack();				
		$MS->add('unittest', 'This is a test message', MS_NORMAL);
		$this->assertArrayHasKey('messages', $_SESSION);		
	}
	
	public function testMessages() {
		$_SESSION = array();
		$MS = new Message_Stack();
		$upper_bound = rand(5, 9);
		for($i=1;$i<=$upper_bound;$i++) {
			$MS->add('unittest', 'This is a test message.', MS_NORMAL);
		}
		$unittest_messages = $MS->messages('unittest');
		$other_messages = $MS->messages();
		$this->assertType("string", $unittest_messages);
		
		$this->assertEquals(0, $MS->count());
	}
	
	public function testCount() {
		$_SESSION = array();
		$MS = new Message_Stack();
		
		$upper_bound = rand(5, 9);
		for($i=1;$i<=$upper_bound;$i++) {
			$MS->add('unittest', 'This is a test message.', MS_NORMAL);
			$this->assertEquals($i, $MS->count());
		}
		$this->assertEquals($upper_bound, $MS->count('unittest'));
		$this->assertEquals(count($_SESSION['messages']), $MS->count('unittest'));
	}
}
?>