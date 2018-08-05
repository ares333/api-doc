<?php
namespace Ares333\Yaf\Helper
{

    /**
     * do `new Functions()` to load functions into root namespace
     */
    class Functions
    {

        static function printr(...$args)
        {
            foreach ($args as $v) {
                if (is_scalar($v)) {
                    echo $v . "\n";
                } else {
                    print_r($v);
                }
            }
            exit();
        }

        static function vardump(...$args)
        {
            call_user_func_array('var_dump', $args);
            exit();
        }

        static function get_instance($name, ...$args)
        {
            static $instances = array();
            $functionStringValue = null;
            $functionStringValue = function ($var) use (&$functionStringValue) {
                if (is_iterable($var)) {
                    $str = '';
                    foreach ($var as $k => $v) {
                        $str .= $k . call_user_func($functionStringValue, $v);
                    }
                    return $str;
                } elseif (is_object($var)) {
                    return spl_object_hash($var);
                } else {
                    return (string) $var;
                }
            };
            $key = md5(strtolower($name) . $functionStringValue($args));
            if (! isset($instances[$key])) {
                $reflection = new \ReflectionClass($name);
                $object = $reflection->newInstanceWithoutConstructor();
                $method = $reflection->getConstructor();
                if (isset($method)) {
                    $method->setAccessible(true);
                    $method->invokeArgs($object, $args);
                }
                $instances[$key] = $object;
            }
            return $instances[$key];
        }

        static function array_dict(array $list, $columns = null, $primary = null,
            $group = null, $primarySeperator = null)
        {
            if (! isset($primarySeperator)) {
                $primarySeperator = '_';
            }
            if (! isset($primary)) {
                $primary = array();
            }
            if (is_string($primary)) {
                $primary = array(
                    $primary
                );
            }
            if (is_string($group)) {
                $group = array(
                    $group
                );
            }
            $listNew = array();
            foreach ($list as $v) {
                $key = array();
                foreach ($primary as $v1) {
                    if (is_object($v)) {
                        $key[] = $v->$v1;
                    } else {
                        $key[] = $v[$v1];
                    }
                }
                if (empty($key)) {
                    unset($key);
                } else {
                    $key = implode($primarySeperator, $key);
                }
                if (isset($columns)) {
                    $vNew = array();
                    if (is_array($columns)) {
                        foreach ($columns as $k1 => $v1) {
                            if (is_int($k1)) {
                                $k1 = $v1;
                            }
                            if (is_object($v)) {
                                $vNew[$k1] = $v->$v1;
                            } else {
                                $vNew[$k1] = $v[$v1];
                            }
                        }
                    } else {
                        if (is_object($v) && isset($v->$columns)) {
                            $vNew = $v->$columns;
                        } else if (isset($v[$columns])) {
                            $vNew = $v[$columns];
                        } else {
                            continue;
                        }
                    }
                } else {
                    $vNew = $v;
                }
                if (is_object($v)) {
                    settype($vNew, 'object');
                }
                if (isset($group)) {
                    $vGroup = &$listNew;
                    foreach ($group as $v2) {
                        if (is_object($v)) {
                            if (isset($vGroup) &&
                                array_key_exists($v->$v2, $vGroup)) {
                                $vGroup = &$vGroup[$v->$v2];
                            } else {
                                $vGroup[$v->$v2] = array();
                                $vGroup = &$vGroup[$v->$v2];
                            }
                        } else {
                            if (isset($vGroup) &&
                                array_key_exists($v[$v2], $vGroup)) {
                                $vGroup = &$vGroup[$v[$v2]];
                            } else {
                                $vGroup[$v[$v2]] = array();
                                $vGroup = &$vGroup[$v[$v2]];
                            }
                        }
                    }
                    if (isset($key)) {
                        $vGroup[$key] = $vNew;
                    } else {
                        $vGroup[] = $vNew;
                    }
                    unset($vGroup);
                } else {
                    if (isset($key)) {
                        $listNew[$key] = $vNew;
                    } else {
                        $listNew[] = $vNew;
                    }
                }
            }
            return $listNew;
        }
    }
}
namespace
{

    use Ares333\Yaf\Helper\Functions;
    if (! function_exists('printr')) {

        /**
         *
         * @param mixed ...$args
         */
        function printr(...$args)
        {
            return call_user_func_array(Functions::class . '::printr', $args);
        }
    }

    if (! function_exists('vardump')) {

        /**
         *
         * @param mixed ...$args
         */
        function vardump(...$args)
        {
            return call_user_func_array(Functions::class . '::vardump', $args);
        }
    }

    if (! function_exists('get_instance')) {

        /**
         *
         * @param string $name
         * @param mixed ...$args
         * @return object
         */
        function get_instance($name, ...$args)
        {
            array_unshift($args, $name);
            return call_user_func_array(Functions::class . '::get_instance',
                $args);
        }
    }

    if (! function_exists('array_dict')) {

        /**
         *
         * @param array $list
         * @param mixed $columns
         * @param mixed $primary
         * @param mixed $group
         * @param string $primarySeperator
         * @return array
         */
        function array_dict(array $list, $columns = null, $primary = null, $group = null,
            $primarySeperator = null)
        {
            return call_user_func(Functions::class . '::array_dict', $list,
                $columns, $primary, $group, $primarySeperator);
        }
    }
}