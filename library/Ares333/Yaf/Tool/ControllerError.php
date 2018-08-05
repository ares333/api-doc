<?php
namespace Ares333\Yaf\Tool;

use Exception;
use Yaf\Controller_Abstract;
use Ares333\Yaf\Helper\Error;
use Yaf\Exception\LoadFailed\Module;
use Yaf\Exception\LoadFailed\Controller;
use Yaf\Exception\LoadFailed\Action;

class ControllerError extends Controller_Abstract
{

    function errorAction(Exception $exception)
    {
        if ($this->is404($exception)) {
            $this->error404Action();
            return false;
        } else {
            $this->error500Action();
            Error::catchException($exception);
        }
        return false;
    }

    function error500Action()
    {
        if (! $this->getRequest()->isCli()) {
            http_response_code(500);
            echo '500 Internal Server Error' . "\n";
        }
        return false;
    }

    function error404Action()
    {
        if (! $this->getRequest()->isCli()) {
            http_response_code(404);
        }
        echo '404 Not Found' . "\n";
        return false;
    }

    function error403Action()
    {
        if (! $this->getRequest()->isCli()) {
            http_response_code(403);
        }
        echo '403 Forbidden' . "\n";
        return false;
    }
    
    /**
     * useful for redirect outside controller
     * @return boolean
     */
    function dummyAction(){
        return false;
    }

    /**
     * is 404
     *
     * @param Exception $exception
     * @return boolean
     */
    protected function is404($exception)
    {
        return $exception instanceof Module || $exception instanceof Controller ||
             $exception instanceof Action;
    }
}