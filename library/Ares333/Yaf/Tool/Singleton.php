<?php
namespace Ares333\Yaf\Tool;

use Ares333\Yaf\Helper\Functions;

trait Singleton {

    /**
     * perfect singleton
     *
     * @return self
     */
    public static function getInstance()
    {
        $args = func_get_args();
        array_unshift($args, get_called_class());
        return call_user_func_array(Functions::class . '::get_instance', $args);
    }
}