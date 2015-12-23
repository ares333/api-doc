<?php
class LoginModel extends AbstractModel {
	private function getSession() {
		static $session;
		if (! isset ( $session )) {
			$dir = APP_PATH . '/runtime/session';
			if (! is_dir ( $dir )) {
				mkdir ( $dir, 0755, true );
			}
			$expire = 7 * 24 * 3600;
			Zend_Session::setOptions ( array (
					'cookie_lifetime' => $expire,
					'gc_maxlifetime' => $expire,
					'save_path' => $dir
			) );
			Zend_Session::start ();
			$session = new Zend_Session_Namespace ( __METHOD__, Zend_Session_Namespace::SINGLE_INSTANCE );
		}
		return $session;
	}
	function checkLogin() {
		return 'yes' === $this->getSession ()->isLogin;
	}
	function login($username, $password) {
		if ($this->checkLogin ()) {
			return true;
		}
		if (! empty ( $username )) {
			$usernameValid = 'admin';
			$passwordValid = 'admin';
			if ($username == $usernameValid && $password == $passwordValid) {
				$this->getSession ()->isLogin = 'yes';
				return true;
			}
		}
		return false;
	}
	function logout() {
		unset ( $this->getSession ()->isLogin );
		return true;
	}
}