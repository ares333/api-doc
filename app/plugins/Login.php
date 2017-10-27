<?php
use Yaf\Plugin_Abstract;
use Yaf\Response_Abstract;
use Yaf\Request_Abstract;

class LoginPlugin extends Plugin_Abstract
{

    function preDispatch(Request_Abstract $request, Response_Abstract $response)
    {
        $controllerName = strtolower($request->getControllerName());
        $exclude = array(
            'index',
            'error',
            'login'
        );
        if (! in_array($controllerName, $exclude)) {
            $model = LoginModel::getInstance();
            if (false == $model->checkLogin()) {
                $request->setControllerName('Login')->setActionName('index');
            }
        }
    }
}