<?php
//define for our message warning levels...
define('MS_NORMAL', 'normal', false);
define('MS_SUCCESS', 'success', false);
define('MS_WARNING', 'warning', false);
define('MS_ERROR', 'error', false);

/**
 * Class that acts as a global place for stacking messages.
 */
class Message_Stack {
	private $_stack = array();

	public function __construct() {
		$this->_stack = exists('messages', $_SESSION, array());
	}

	/**
	 * Add something to the message stack.
	 *
	 * @param message_type Type of the message. Arbitrary string you can use to group like messages. I.e. "login"/"cart"
	 * @param message The actual message you want to store.
	 * @param warning_level How serious is this message? That serious? Wow. o_0
	 */
	public function add($message_type, $message, $warning_level = MS_NORMAL) {
		$message = trim($message);
		if(false == empty($message)) {
			$this->_stack[] = array(
				'type' => $message_type,
				'level' => $warning_level,
				'message' => $message
			);
		}
		$this->_write();
	}

	/**
	 * Renders all or part of the message stack and then flushes those messages from the stack.
	 *
	 * @param type Type of messages you want to render.
	 */
	public function messages($type = null) {
		$message_list = array();
		if(false == is_null($type)) {
			foreach($this->_stack as $i => $msg) {
				if($msg['type'] == $type) {
					$message_list[] = $msg;
				}
			}
		} else {
			$message_list = $this->_stack;
		}

		$message_string = null;
		foreach($message_list as $i => $msg) {
			$message_string .= '<div class="' . $msg['level'] . '">' . $msg['message'] . '</div>';
		}

		$this->_flush($type);
		return $message_string;
	}

	/**
	 * Returns the number of messages for a given type (or no type).
	 *
	 * @param type Type of messages you want to count.
	 * @return Returns the number of messages for a given type.
	 */
	public function count($type = null) {
		$count = 0;
		if(true == is_null($type)) {
			$count = count($this->_stack);
		} else {
			foreach($this->_stack as $i => $msg) {
				if($msg['type'] == $type) {
					$count++;
				}
			}
		}
		return $count;
	}

	/**
	 * Removes messages of a given type from the stack.
	 */
	private function _flush($message_type = null) {
		if(false == is_null($message_type)) {
			foreach($this->_stack as $i => $msg) {
				if($msg['type'] == $message_type) {
					unset($this->_stack[$i]);
				}
			}
		} else {
			$this->_stack = array();
		}
		$this->_write();
	}

	/**
	 * Writes the message stack to the session.
	 */
	private function _write() {
		$_SESSION['messages'] = $this->_stack;
	}
}
?>