<?php
class LoginPlugin extends Yaf_Plugin_Abstract {
	function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
		$controllerName = strtolower ( $request->getControllerName () );
		$exclude = array (
				'api',
				'index',
				'error',
				'login'
		);
		if (! in_array ( $controllerName, $exclude )) {
			$model = LoginModel::getInstance ();
			if (false == $model->checkLogin ()) {
				$request->setControllerName ( 'Login' )->setActionName ( 'index' );
			}
		}
	}
}