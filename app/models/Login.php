<?php
class LoginModel extends AbstractModel {
	function __construct() {
		$expire = 30 * 86400;
		session_set_cookie_params ( $expire );
		ini_set ( 'session.gc_maxlifetime', $expire );
		session_start ();
	}
	function checkLogin() {
		return ! empty ( $_SESSION ['isLogin'] ) && 'yes' === $_SESSION ['isLogin'];
	}
	function login($username, $password) {
		if ($this->checkLogin ()) {
			return true;
		}
		if (! empty ( $username )) {
			$usernameValid = 'admin';
			$passwordValid = 'admin';
			if ($username == $usernameValid && $password == $passwordValid) {
				$_SESSION ['isLogin'] = 'yes';
				return true;
			}
		}
		return false;
	}
	function logout() {
		unset ( $_SESSION ['isLogin'] );
		return true;
	}
}