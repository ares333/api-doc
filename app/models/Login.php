<?php
use Yaf\Application;
use Zend\Session\SessionManager;
use Zend\Session\Container;

class LoginModel extends AbstractModel
{

    private function getSession()
    {
        static $session;
        if (! isset($session)) {
            $dir = Application::app()->getAppDirectory() . '/../runtime/session';
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $sessionManager = new SessionManager();
            $config = $sessionManager->getConfig();
            $config->setCookieLifetime(90 * 86400);
            $config->setGcMaxlifetime(90 * 86400);
            $config->setSavePath($dir);
            $session = new Container(
                str_replace(
                    [
                        ':',
                        '\\'
                    ], '', __METHOD__), $sessionManager);
        }
        return $session;
    }

    function checkLogin()
    {
        return 'yes' === $this->getSession()->isLogin;
    }

    function login($username, $password)
    {
        if ($this->checkLogin()) {
            return true;
        }
        if (! empty($username)) {
            $usernameValid = 'admin';
            $passwordValid = 'admin';
            if ($username == $usernameValid && $password == $passwordValid) {
                $this->getSession()->isLogin = 'yes';
                return true;
            }
        }
        return false;
    }

    function logout()
    {
        unset($this->getSession()->isLogin);
        return true;
    }
}