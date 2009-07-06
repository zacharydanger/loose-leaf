<?php
require_once 'Object.php';
require_once 'User.php';

/**
 * Manages Password Tokens.
 */
class Password_Token extends Object {
	protected $_table = 'user_password_tokens';
	protected $_table_id = 'token_id';

	protected $_set_hooks = array('user_type' => '_setUserType');

	protected function _setUserType($user_type) {
		$user_type = trim($user_type);
		$good_types = array(User::TYPE_CUSTOMER, User::TYPE_SALES, User::TYPE_ADMIN);
		if(true == in_array($user_type, $good_types, true)) {
			return $user_type;
		} else {
			throw new Exception('Bad user type!');
		}
	}

	/**
	 * Looks up a User account by the reset token.
	 */
	public function tokenFactory($token, $user_type = User::TYPE_CUSTOMER) {
		$PT = new Password_Token($token, 'token');
		
		if(false == $PT->exists()) {
			$PT->user_type = $user_type;
		}

		switch($PT->user_type) {
			case User::TYPE_CUSTOMER: {
				return Object_Factory::OF()->newObject('Customer', $PT->user_id);
				break;
			}

			case User::TYPE_SALES: {
				return Object_Factory::OF()->newObject('Sales_Rep', $PT->user_id);
				break;
			}

			case User::TYPE_ADMIN: {
				return Object_Factory::OF()->newObject('Admin', $PT->user_id);
				break;
			}

			default: {
				throw new Exception("Couldn't determine the User type of this Password Token.");
				break;
			}
		}
	}
}
?>