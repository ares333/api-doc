<?php
namespace Ares333\Yaf\Helper;

class Error
{

    /**
     * error to exception
     *
     * @throws \ErrorException
     */
    static function error2exception()
    {
        set_error_handler(
            function ($errno, $errstr, $errfile, $errline) {
                $r = error_reporting();
                if ($r & $errno) {
                    $exception = new \ErrorException($errstr, null, $errno,
                        $errfile, $errline);
                    if ($errno == E_USER_ERROR || $errno == E_RECOVERABLE_ERROR) {
                        throw $exception;
                    }
                    static::catchException($exception);
                }
            });
    }

    /**
     *
     * @param int $severity
     * @return string|null
     */
    static function severity2string($severity)
    {
        static $map;
        if (! isset($map)) {
            $map = get_defined_constants(true)['Core'];
            foreach (array_keys($map) as $v) {
                if (0 !== strpos($v, 'E_')) {
                    unset($map[$v]);
                }
            }
            $map = array_flip($map);
        }
        if (array_key_exists($severity, $map)) {
            return $map[$severity];
        }
    }

    /**
     * Deal with exception
     *
     * @param \Exception $exception
     * @param bool $return
     */
    static function catchException($exception, $return = false)
    {
        $str = '';
        if ($exception instanceof \ErrorException) {
            $str .= static::severity2string($exception->getSeverity()) . ': ';
        }
        $str .= $exception->__toString();
        if (ini_get('log_errors')) {
            error_log($str);
        }
        if (ini_get('display_errors')) {
            if ($return) {
                return $str;
            } else {
                echo $str . "\n\n";
            }
        }
    }
}