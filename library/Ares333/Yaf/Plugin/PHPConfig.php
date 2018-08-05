<?php
namespace Ares333\Yaf\Plugin;

use Yaf\Plugin_Abstract;
use Yaf\Request_Abstract;
use Yaf\Response_Abstract;
use Yaf\Application;
use Ares333\Yaf\Helper\File;

class PHPConfig extends Plugin_Abstract
{

    function routerStartup(Request_Abstract $request,
        Response_Abstract $response)
    {
        $php = Application::app()->getConfig()->get('php');
        if (isset($php)) {
            $keyWithDir = array(
                'error_log'
            );
            $items = $php->toArray();
            while (! empty($items)) {
                foreach ($items as $k => $v) {
                    unset($items[$k]);
                    if (is_array($v)) {
                        $k .= '.' . key($v);
                        $items[$k] = current($v);
                    } else {
                        if (in_array($k, $keyWithDir)) {
                            if (! File::isAbsolute($v)) {
                                $v = Application::app()->getAppDirectory() .
                                     '/../' . $v;
                            }
                        }
                        ini_set($k, $v);
                    }
                }
            }
        }
    }
}
