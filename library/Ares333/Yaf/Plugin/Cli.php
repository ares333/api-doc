<?php
namespace Ares333\Yaf\Plugin;

use Yaf\Plugin_Abstract;
use Yaf\Request_Abstract;
use Yaf\Response_Abstract;
use Yaf\Application;

class Cli extends Plugin_Abstract
{

    private $modules;

    function __construct()
    {
        $this->modules = Application::app()->getModules();
    }

    /**
     * parse module
     *
     * @param Request_Abstract $request
     * @param Response_Abstract $response
     */
    function routerStartup(Request_Abstract $request,
        Response_Abstract $response)
    {
        $uri = getopt('0:');
        if (isset($uri[0])) {
            $uri = $uri[0];
            $moduleName = null;
            if (preg_match('/^[^\?]*%/i', $uri)) {
                list ($moduleName, $uri) = explode('%', $uri, 2);
            }
            $moduleName = ucfirst(strtolower($moduleName));
            if (in_array($moduleName, $this->modules)) {
                $request->setModuleName($moduleName);
            }
            if (false === strpos($uri, '?')) {
                $args = array();
            } else {
                list ($uri, $args) = explode('?', $uri, 2);
                parse_str($args, $args);
            }
            foreach ($args as $k => $v) {
                $request->setParam($k, $v);
            }
            $request->setRequestUri($uri);
            if ($request->isRouted() && ! empty($uri)) {
                if (false !== strpos($uri, '/')) {
                    list ($controller, $action) = explode('/', $uri);
                    $request->setActionName($action);
                } else {
                    $controller = $uri;
                }
                $request->setControllerName(ucfirst(strtolower($controller)));
            }
        }
    }
}