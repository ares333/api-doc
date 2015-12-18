<?php
class LoginController extends Yaf_Controller_Abstract {
	function indexAction() {
		$redirect = $this->getRequest ()->getQuery ( 'redirect', '/' );
		$model = LoginModel::getInstance ();
		if ($model->checkLogin ()) {
			$this->redirect ( $redirect );
			return false;
		}
		$request = $this->getRequest ();
		$username = $request->getPost ( 'username' );
		$password = $request->getPost ( 'password' );
		$out = array ();
		if (! empty ( $username )) {
			if ($model->login ( $username, $password )) {
				$this->redirect ( $redirect );
				return false;
			} else {
				$out ['error'] = '登录失败';
			}
		}
		$this->getView ()->assign ( $out );
	}
	function logoutAction() {
		$redirect = $this->getRequest ()->getQuery ( 'redirect', '/' );
		if (LoginModel::getInstance ()->logout ()) {
			$this->redirect ( $redirect );
		} else {
			echo '退出登录失败，请联系管理员';
		}
		return false;
	}
}