<?php
namespace Ares333\Yaf\Plugin;

use Yaf\Plugin_Abstract;
use Yaf\Request_Abstract;
use Yaf\Response_Abstract;
use Yaf\Application;

class Module extends Plugin_Abstract
{

    const URI = 0b0001;

    const HOST = 0b0010;

    private $modules;

    private $config = 0b0000;

    function __construct($config)
    {
        $this->config = $config;
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
        if (self::URI == (self::URI & $this->config)) {
            $this->uri($request, $response);
        }
        if (self::HOST == (self::HOST & $this->config)) {
            $this->host($request, $response);
        }
    }

    /**
     * uri part less than 3
     *
     * @param Request_Abstract $request
     * @param Response_Abstract $response
     */
    private function uri(Request_Abstract $request, Response_Abstract $response)
    {
        $uri = explode('/', trim(strtolower($request->getRequestUri()), '/'));
        if (count($uri) < 3) {
            $moduleName = ucfirst(strtolower($uri[0]));
            if (in_array($moduleName, $this->modules)) {
                $request->setModuleName($moduleName);
                unset($uri[0]);
                $uri = '/' . implode('/', $uri);
                $request->setRequestUri($uri);
            }
        }
    }

    /**
     *
     * @param Request_Abstract $request
     * @param Response_Abstract $response
     */
    private function host(Request_Abstract $request, Response_Abstract $response)
    {
        $host = explode('.', strtolower($request->getServer('HTTP_HOST')));
        foreach ($host as $v) {
            $moduleName = ucfirst(strtolower($v));
            if (in_array($moduleName, $this->modules)) {
                $request->setModuleName($moduleName);
                break;
            }
        }
    }
}