<?php
use Yaf\Bootstrap_Abstract;
use Yaf\Dispatcher;
use Yaf\Loader;
use Ares333\Yaf\Tool\SmartyView;
use Ares333\Yaf\Tool\Functions;
use Ares333\Yaf\Helper\Error;
use Ares333\Yaf\Plugin\PhpConfig;
use Ares333\Yaf\Plugin\Cli;

class Bootstrap extends Bootstrap_Abstract
{

    function _initAutoload(Dispatcher $dispatcher)
    {
        $libraryPath = $dispatcher->getApplication()->getAppDirectory() .
             '/library';
        Loader::getInstance($libraryPath, $libraryPath);
        Loader::import('vendor/autoload.php');
    }

    function _initDebug(Dispatcher $dispatcher)
    {
        new Functions();
        Error::error2exception();
    }

    function _initPlugin(Dispatcher $dispatcher)
    {
        $dispatcher->registerPlugin(new PhpConfig());
        $dispatcher->registerPlugin(new Cli());
        $dispatcher->registerPlugin(new LoginPlugin());
    }

    function _initView(Dispatcher $dispatcher)
    {
        $view = new SmartyView();
        $dispatcher->setView($view);
    }
}