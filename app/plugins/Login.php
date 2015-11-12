<?php
class LoginPlugin extends Yaf_Plugin_Abstract{
	function preDispatch(Yaf_Request_Abstract $request,Yaf_Response_Abstract $response){
		$controllerName=strtolower($request->getControllerName());
		if(0===strpos($controllerName,'doc')){
			$model = LoginModel::getInstance ();
			if(false==$model->checkLogin()){
				$request->setControllerName('Login')->setActionName('index');
			}
		}
	}
}