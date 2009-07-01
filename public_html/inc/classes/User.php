<?php
require_once 'Object.php';
require_once 'Mailer.php';

/**
 * Generic class for handling Users.
 */
abstract class User extends Object {
	protected $_set_hooks = array('email' => 'setEmail');

	private $_session;

	public function __construct($ID = 0, $field = null) {
		parent::__construct($ID, $field);
		$this->_loadSession();
	}

	private function _loadSession() {
		if(true == $this->exists()) {
			$sql = "SELECT user_session_id
				  FROM `user_sessions`
				  WHERE user_id = '" . intval($this->ID) . "'
				  	AND user_type = '" . db_input($this->_user_type) . "'";
			$query = db_query($sql);
			$user_session_id = 0;
			while($query->num_rows > 0 && $rec = $query->fetch_assoc()) {
				$user_session_id = intval($rec['user_session_id']);
			}
			$this->_session = new User_Session($user_session_id);
			if(false == $this->_session->exists()) {
				$this->_session->user_type = $this->_user_type;
				$this->_session->user_id = $this->ID;
			}
		}
	}

	/**
	 * Sets the email.
	 *
	 * @param email The new email address.
	 * @return Returns the email address after set is run. On success, matches the email address from $email.
	 */
	public function setEmail($email) {
		$email = filter_var($email, FILTER_SANITIZE_EMAIL); //sanitize the email
		$email_set = false;
		$sql = "SELECT count(`" . $this->_table_id . "`) AS count
			FROM `" . $this->_table . "`
			WHERE email = '" . db_input($email) . "'
				AND `" . $this->_table_id . "` != '" . intval($this->ID) . "'";
		$query = db_query($sql);
		while($query->num_rows > 0 && $c = $query->fetch_assoc()) {
			if(0 == intval($c['count']) && true == validate_email($email)) {
				$this->_data['email'] = $email;
				$email_set = true;
			}
		}
		return $this->_data['email'];
	}

	/**
	 * @return Returns the "type" of this User class.
	 */
	public function getUserType() {
		if(true == isset($this->_user_type)) {
			return $this->_user_type;
		} else {
			throw new Exception("User type not set for this class.");
		}
	}

	/**
	 * Given an email address and password this will load the object.
	 *
	 * @param email Email of the User to be logged in.
	 * @param password Password of the User to be loggedin.
	 * @return Returns true if successfully logged in, false otherwise.
	 */
	public function login($email, $password) {
		$login_success = false;
		$password = trim($password);
		$class = get_class($this);
		$C = new $class($email, 'email');
		if(true == $C->exists() && passwordify($password, $C->salt) == $C->getPassword()) {
			$this->_load($C->getID());
			$this->_loadSession();
			if(true == $this->exists()) {
				$this->_session->generateToken();
				$this->_session->write();
				$login_success = true;
				$this->write();
			}
		}
		return $login_success;
	}

	/**
	 * Set the password for the Customer.
	 *
	 * @param new_password New password for the User.
	 * @param old_password Old password. Required for setting a user's password after a password already exists.
	 * @return Returns true if the password was sucessfully changed. False, otherwise.
	 */
	public function setPassword($new_password, $old_password = null) {
		$password_changed = false;
		$new_password = trim($new_password);
		$current_password = $this->_data['password'];

		//setting a new password from an old password
		if(false == is_null($current_password) && false==empty($current_password) && $current_password != passwordify($old_password, $this->salt)) {
			$error = true;
		}

		//the new password isn't good (too short or null)
		if(false == $this->_goodPassword($new_password)) {
			$error = true;
		}

		if(false == $error) {
			$this->_resalt(); //regenerate our salt, just for funsies.
			parent::__call('setPassword', passwordify($new_password, $this->salt));
			$password_changed = true;
		}
		return $password_changed;
	}

	/**
	 * Returns true if a given password value is considered decent.
	 */
	private function _goodPassword($password) {
		$good_password = true;
		$password = trim($password);

		//check minimum length
		if(strlen($password) < MIN_PASSWORD_LENGTH || true == is_null($password)) {
			$good_password = false;
		}

		return $good_password;
	}

	/**
	 * Resets the password to a new password using a token.
	 *
	 * @param new_password The new password for the user.
	 * @param token The password reset token.
	 * @return Returns true if password is successfully reset, false otherwise.
	 */
	public function resetPassword($new_password, $token) {
		$new_password = trim($new_password);
		$password_reset = false;
		if(intval($this->_ID) > 0 && true == $this->_goodPassword($new_password)) {
			$sql = "SELECT upt.expiration
				FROM `user_password_tokens` upt
				WHERE user_id = '" . intval($this->_ID) . "'
					AND user_type = '" . db_input($this->getUserType()) . "'
					AND token = '" . db_input($token) . "'";
			$query = db_query($sql);
			while($query->num_rows > 0 && $t = $query->fetch_assoc()) {
				$expiration = strtotime($t['expiration']);
			}
			if($expiration > time()) {
				$this->_resalt();
				parent::__call('setPassword', passwordify($new_password, $this->salt));
				$this->write();
				$password_reset = true;
				$sql = "DELETE FROM `user_password_tokens`
						WHERE user_id = '" . intval($this->_ID) ."'
							AND user_type = '" . db_input(get_class($this)) . "'";
				db_query($sql);
			}
		}
		return $password_reset;
	}

	public function newToken() {
		$token = null;
		if(true == $this->exists()) {
			$token = $this->_session->generateToken();
		}
		return $token;
	}

	/**
	 * Override the write() method to produce a unique session key no matter what.
	 */
	public function write() {
		parent::write();
		$this->_writeSession();
	}

	protected function _writeSession() {
		if(true == $this->exists() && true == isset($this->_session)) {
			$this->_session->user_id = $this->ID;
			$this->_session->user_type = $this->_user_type;
			if(false == is_null($this->_session->token)) {
				$this->_session->write();
			}
		}
	}

	/**
	 * Override the _insert() method just a bit.
	 */
	protected function _insert() {
		$this->date_registered = date('Y-m-d H:i:s');
		parent::_insert();
		$this->_loadSession();
	}

	/**
	 * @return Returns the session token for this User.
	 */
	public function getToken() {
		$token = null;
		if(true == $this->exists() && true == isset($this->_session)) {
			$token = $this->_session->token;
		}
		return $token;
	}

	/**
	 * Logs the user out. Imagine that.
	 */
	public function logout() {
		if(true == $this->exists() && true == isset($this->_session)) {
			$this->_session->delete();
		}
	}

	/**
	 * Resalts the User.
	 */
	protected function _resalt() {
		$salt = sha1(md5(uniqid(rand(), true)));
		$this->salt = $salt;
	}
}

/* Color me deprecated.
function recover_user_password(User $U) {
	$good_token = false;
	$password_token = '';
	$user_class = $U->getUserType();
	while(false == $good_token) {
		$password_token = random_value(16);
		$sql = "SELECT `upt`.`token_id`
			FROM `user_password_tokens` upt
			WHERE upt.token = '" . db_input($password_token) . "'
				AND upt.expiration > now()
				AND upt.user_type = '" . db_input($user_class) . "'";
		$query = db_query($sql);
		if(0 == $query->num_rows) {
			$good_token = true;
		}
	}

	$token_data = array(
			'user_id' => intval($U->getID()),
			'token' => $password_token,
			'expiration' => date('Y-m-d H:i:s', (time() + PASSWORD_TOKEN_EXPIRATION)),
			'user_type' => $user_class);
	db_perform('user_password_tokens', $token_data, SQL_INSERT);
	$link = SITE_URL . '/reset_password/?token=' . $password_token;
	
	$M = new Mailer();
	$M->addTo($U->email, $U->name);
	$M->setSubject("1000Bulbs.com Password Recovery");
	$body = "Please follow the link below to recover your 1000bulbs.com password.\n\n";
	$body .= $link . "\n\n";
	$body .= "If you didn't request a password reset, you may ignore this message.\n\n";
	$body .= "--\nThis is an automated message from 1000bulbs.com, do not reply.";
	$M->setBody($body);
	try {
		$M->send();
	} catch(Exception $e) {
		//do nothing!
	}
}
*/

function write_user_session_cookie(User $U, $cookie_name = 'session_id') {
	global $_SERVER;
	$expires = time() + (24 * 60 * 60); //keep them logged in for a day? Why not.
	setcookie($cookie_name, $U->getToken(), $expires, '/', $_SERVER['SERVER_NAME']);
}
?>
