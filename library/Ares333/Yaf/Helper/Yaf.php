<?php
namespace Ares333\Yaf\Helper;

use Yaf\Application;

class Yaf
{

    /**
     * Get yaf application config with default value
     *
     * @return string
     */
    static function getConfig($name)
    {
        $defaultMap = array(
            'ext' => 'php',
            'dispatcher.defaultModule' => 'Index',
            'dispatcher.defaultController' => 'Index',
            'dispatcher.defaultAction' => 'index',
            'view.ext' => 'phtml'
        );
        if (array_key_exists($name, $defaultMap)) {
            $default = $defaultMap[$name];
        }
        $name = explode('.', $name);
        $config = Application::app()->getConfig();
        foreach (array(
            'application',
            'yaf'
        ) as $v) {
            if (isset($config->$v)) {
                $flag = true;
                $res = $config->$v;
                foreach ($name as $v1) {
                    if (isset($res->$v1)) {
                        $res = $res->$v1;
                    } else {
                        $flag = false;
                    }
                }
                if ($flag) {
                    break;
                }
            }
        }
        if (true === $flag) {
            return $res;
        }
        if (isset($default)) {
            return $default;
        }
    }
}