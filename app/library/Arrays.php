<?php

/**
 *
 * @author admin@phpdr.net
 *
 */
class Arrays
{

    /**
     *
     * @param array $arr
     * @return boolean
     */
    static function emptyr($arr)
    {
        if (empty($arr)) {
            return true;
        } else {
            if (is_array($arr)) {
                foreach ($arr as $v) {
                    if (false === call_user_func(__METHOD__, $v)) {
                        return false;
                    }
                }
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     *
     * @param array $arr
     * @param mixed $key
     */
    static function current($arr, $key)
    {
        if (! is_array($key)) {
            $key = array(
                $key
            );
        }
        foreach ($key as $v) {
            if (array_key_exists($v, $arr)) {
                $arr = $arr[$v];
            }
        }
        return $arr;
    }

    /**
     * recursive walk all value
     *
     * @param array $arr
     * @param callable $cb
     */
    static function walkr(&$arr, $cb)
    {
        foreach ($arr as $k => &$v) {
            $cb($v, $k);
            if (is_array($v)) {
                call_user_func_array(__METHOD__,
                    array(
                        &$v,
                        &$cb
                    ));
            }
        }
    }

    /**
     * recursive uasort
     *
     * @param array $arr
     * @param callable $cb
     */
    static function uasortr(array &$arr, $cb)
    {
        uasort($arr,
            function ($a, $b) use (&$cb) {
                return $cb($a, $b);
            });
        foreach ($arr as &$v) {
            if (is_array($v)) {
                call_user_func_array(__METHOD__,
                    array(
                        &$v,
                        $cb
                    ));
            }
        }
    }

    /**
     * recursive uksort
     *
     * @param array $arr
     * @param callable $cb
     */
    static function uksortr(array &$arr, $cb)
    {
        uksort($arr,
            function ($a, $b) use ($cb) {
                return $cb($a, $b);
            });
        foreach ($arr as &$v) {
            if (is_array($v)) {
                call_user_func_array(__METHOD__,
                    array(
                        &$v,
                        $cb
                    ));
            }
        }
    }

    /**
     *
     * @param string $search
     * @param mixed $replace
     * @param array $subject
     * @param bool $loop
     *            array change by reference $kNew may be added infinitely
     */
    static function pregReplaceKeyr($search, $replacement, array &$subject,
        $loop = false)
    {
        static $i = 0;
        static $depth = 0;
        static $new = array();
        if ($depth == 0) {
            $new = array();
        }
        foreach ($subject as $k => &$v) {
            if (is_array($v)) {
                $depth ++;
                call_user_func_array(__METHOD__,
                    array(
                        $search,
                        $replacement,
                        &$v,
                        $loop
                    ));
                $depth --;
            }
            if (is_array($k)) {
                $depth ++;
                call_user_func_array(__METHOD__,
                    array(
                        $search,
                        $replacement,
                        &$k,
                        $loop
                    ));
                $depth --;
            } else {
                if (false == $loop) {
                    if (in_array($k, $new)) {
                        continue;
                    }
                }
                $type = gettype($k);
                if (is_callable($replacement)) {
                    $func = 'preg_replace_callback';
                } else {
                    $func = 'preg_replace';
                }
                $kNew = $func($search, $replacement, (string) $k);
                settype($kNew, $type);
                if ($kNew !== $k) {
                    $subject[$kNew] = $v;
                    unset($subject[$k]);
                    if (false == $loop) {
                        $new[] = $kNew;
                    }
                }
            }
        }
    }

    /**
     * group by any level
     * http://blog.phpdr.net/php%E6%97%A0%E6%9E%81%E5%88%86%E7%BB%84.html
     *
     * @param array $list
     * @param mixed $columns
     * @param mixed $group
     * @param mixed $primary
     * @return array
     */
    static function dict(array $list, $columns = null, $group = null, $primary = null)
    {
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
            $key = null;
            foreach ($primary as $v1) {
                $key .= $v[$v1];
            }
            if (isset($columns)) {
                if (is_array($columns) && 1 === count($columns) &&
                     $columns[0] === null) {
                    $vNew = null;
                } else {
                    $vNew = array();
                    if (is_array($columns)) {
                        foreach ($columns as $k1 => $v1) {
                            if (is_int($k1)) {
                                $k1 = $v1;
                            }
                            $vNew[$k1] = $v[$v1];
                        }
                    } else {
                        $vNew = $v[$columns];
                    }
                }
            } else {
                $vNew = $v;
            }
            if (isset($group)) {
                $vGroup = &$listNew;
                foreach ($group as $v2) {
                    if (isset($vGroup) && array_key_exists($v[$v2], $vGroup)) {
                        $vGroup = &$vGroup[$v[$v2]];
                    } else {
                        $vGroup[$v[$v2]] = array();
                        $vGroup = &$vGroup[$v[$v2]];
                    }
                }
                if (isset($key)) {
                    $vGroup[$key] = $vNew;
                } else {
                    $vGroup[] = $vNew;
                }
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

    /**
     *
     * @param mixed $search
     * @param mixed $replacement
     * @param array $subject
     */
    static function pregReplacer($search, $replacement, array &$subject)
    {
        foreach ($subject as $k => &$v) {
            if (is_array($v)) {
                call_user_func_array(__METHOD__,
                    array(
                        $search,
                        $replacement,
                        &$v
                    ));
            } else {
                if (is_callable($replacement) && ! is_string($replacement)) {
                    $func = 'preg_replace_callback';
                } else {
                    $func = 'preg_replace';
                }
                $type = gettype($v);
                $v = $func($search, $replacement, (string) $v);
                settype($v, $type);
            }
        }
    }

    /**
     *
     * @param array $arr1
     * @param array $arr2
     * @param boolean $ignoreNumKey
     */
    static function merger($arr1, $arr2, $ignoreNumKey = false)
    {
        if (! is_array($arr1) || ! is_array($arr2)) {
            return $arr1;
        }
        foreach ($arr2 as $k => $v) {
            if (array_key_exists($k, $arr1)) {
                if (! is_array($arr1[$k])) {
                    if (is_numeric($k) && ! $ignoreNumKey) {
                        $arr1[] = $v;
                    } else {
                        $arr1[$k] = $v;
                    }
                } else {
                    $arr1[$k] = call_user_func_array(__METHOD__,
                        array(
                            $arr1[$k],
                            $v,
                            $ignoreNumKey
                        ));
                }
            } else {
                $arr1[$k] = $v;
            }
        }
        return $arr1;
    }

    /**
     * add prefix recursively
     *
     * @param array $arr1
     * @param array $arr2
     */
    static function prefixr(array &$arr1, array $arr2)
    {
        if (! empty($arr2)) {
            foreach ($arr2 as $k => $v) {
                if (array_key_exists($k, $arr1)) {
                    if (is_array($v)) {
                        $temp = &$arr1[$k];
                        call_user_func(__METHOD__, $temp, $v);
                    } else {
                        $arr1[$k] = $v . $arr1[$k];
                    }
                }
            }
        }
    }

    /**
     * unset recursively by exact key
     *
     * @param array $arr1
     * @param array $arr2
     *            keys to unset
     */
    static function unsetr(array $arr1, array $arr2)
    {
        if (! empty($arr2)) {
            foreach ($arr2 as $k => $v) {
                if (array_key_exists($k, $arr1)) {
                    if (! is_array($v)) {
                        unset($arr1[$k]);
                    } else {
                        $arr1[$k] = call_user_func_array(__METHOD__,
                            array(
                                $arr1[$k],
                                $v
                            ));
                    }
                }
            }
        }
        return $arr1;
    }

    /**
     * add suffix recursively
     *
     * @param array $arr1
     * @param array $arr2
     */
    static function suffixr(&$arr1, $arr2)
    {
        if (! empty($arr2)) {
            foreach ($arr2 as $k => $v) {
                if (array_key_exists($k, $arr1)) {
                    if (is_array($v)) {
                        call_user_func_array(__METHOD__,
                            array(
                                &$arr1[$k],
                                $v
                            ));
                    } else {
                        $arr1[$k] .= $v;
                    }
                }
            }
        }
    }

    /**
     * add wraper recersively
     *
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
    static function wraperr(&$arr1, $arr2)
    {
        if (! empty($arr2)) {
            foreach ($arr2 as $k => $v) {
                if (array_key_exists($k, $arr1)) {
                    if (is_array($v)) {
                        call_user_func_array(__METHOD__,
                            array(
                                &$arr1[$k],
                                $v
                            ));
                    } elseif (is_string($v)) {
                        if (is_string($arr1[$k])) {
                            $arr1[$k] = str_replace('{$' . $k . '}', $arr1[$k],
                                $v);
                        }
                    }
                }
            }
        }
    }

    /**
     * unset by scalar value recursively
     *
     * @param array $array
     * @param mixed $value
     */
    static function unsetByValuer(&$array, $value)
    {
        if (! is_array($value)) {
            $value = array(
                $value
            );
        }
        foreach ($array as $k => &$v) {
            foreach ($value as $v1) {
                if ($v === $v1) {
                    unset($array[$k]);
                } elseif (is_array($v)) {
                    call_user_func_array(__METHOD__,
                        array(
                            &$v,
                            $v1
                        ));
                }
            }
        }
    }

    /**
     * unset by key recursively
     *
     * @param array $array
     * @param mixed $keys
     */
    static function unsetByKey(&$array, $keys)
    {
        if (! is_array($keys)) {
            $keys = array(
                $keys
            );
        }
        foreach ($array as $k => &$v) {
            foreach ($keys as $v1) {
                if ($k === $v1) {
                    unset($array[$k]);
                } elseif (is_array($v)) {
                    call_user_func_array(__METHOD__,
                        array(
                            &$v,
                            $v1
                        ));
                }
            }
        }
    }

    /**
     * unset by recursively
     *
     * @param array $array
     * @param mixed $value
     */
    static function unsetCallbackr(&$array, $callback)
    {
        foreach ($array as $k => &$v) {
            $unset = call_user_func($callback, $k, $v);
            if (true == $unset) {
                unset($array[$k]);
            } else if (is_array($v)) {
                call_user_func_array(__METHOD__,
                    array(
                        &$v,
                        $callback
                    ));
            }
        }
    }

    /**
     * recursive search
     *
     * @param mixed $needle
     * @param array $haystack
     * @param string $strict
     * @return false or array
     */
    static function searchr($needle, $haystack, $strict = false)
    {
        if (false === is_array($haystack)) {
            user_error('haystack is not array', E_USER_WARNING);
            return;
        }
        $key = array();
        $r = array_search($needle, $haystack, $strict);
        if (false === $r) {
            foreach ($haystack as $k => $v) {
                if (is_array($v)) {
                    $t = call_user_func(__METHOD__, $needle, $v, $strict, true);
                    if (false !== $t) {
                        $key = array_merge($key,
                            array(
                                $k
                            ), $t);
                        break;
                    }
                }
            }
        } else {
            $key[] = $r;
        }
        if (empty($key)) {
            return false;
        } else {
            return $key;
        }
    }

    /**
     * preg search by array keys
     *
     * @param mixed $pattern
     * @param array $haystack
     * @return array
     */
    static function pregKeySearch($pattern, $haystack)
    {
        if (! is_array($haystack)) {
            return;
        }
        foreach ($haystack as $k => $v) {
            if (! preg_match($pattern, $k)) {
                unset($haystack[$k]);
            }
        }
        return $haystack;
    }

    /**
     * preg search by array keys
     *
     * @param mixed $pattern
     * @param array $haystack
     * @return array
     */
    static function pregKeySearchr($pattern, $haystack)
    {
        if (! is_array($haystack)) {
            return;
        }
        foreach ($haystack as $k => $v) {
            if (is_array($v)) {
                $haystack[$k] = call_user_func(__METHOD__, $pattern, $v);
            } else {
                if (! preg_match($pattern, $k)) {
                    unset($haystack[$k]);
                }
            }
        }
        return $haystack;
    }

    /**
     * array_values recursive
     *
     * @param array $arr
     */
    static function valuesr($arr, $keys = null)
    {
        if (is_string($keys)) {
            $keys = array(
                $keys
            );
        }
        if (! is_array($arr)) {
            return;
        }
        $res = array();
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $res = array_merge($res, call_user_func(__METHOD__, $v, $keys));
            } else if (! isset($keys) || in_array($k, $keys)) {
                $res[] = $v;
            }
        }
        return array_unique($res);
    }

    /**
     * preg search by array keys
     *
     * @param mixed $pattern
     * @param array $haystack
     * @return array
     */
    static function pregSearch($pattern, $haystack)
    {
        if (! is_array($haystack)) {
            return;
        }
        foreach ($haystack as $k => $v) {
            if (! preg_match($pattern, $v)) {
                unset($haystack[$k]);
            }
        }
        return $haystack;
    }

    /**
     * insert before a key
     *
     * @param array $array
     * @param mixed $key
     * @param mixed $row
     * @return array
     */
    static function insert(array $array, $key, $row)
    {
        $up = array();
        if (is_array($array) && is_array($row) && array_key_exists($key, $array)) {
            foreach ($array as $k => $v) {
                if ($key === $k) {
                    $up[key($row)] = $row[key($row)];
                }
                $up[$k] = array_shift($array);
            }
        }
        return $up;
    }

    /**
     * return values ,not keys
     *
     * @param array $arr
     * @param integer $num
     * @return array
     */
    static function rand($arr, $num = null)
    {
        if (! isset($num))
            $num = 1;
        if (is_array($arr) && is_numeric($num)) {
            $key = array_rand($arr, $num);
            if ($num == 1) {
                $single = true;
            } else {
                $single = false;
            }
            if ($single) {
                $key = array(
                    $key
                );
            }
            $r = array();
            foreach ($key as $v) {
                $r[] = $arr[$v];
            }
            if ($single) {
                $r = array_pop($r);
            }
            return $r;
        }
    }

    /**
     * recursive in_array()
     *
     * @param mixed $needle
     * @param array $haystack
     * @param boolean $strict
     * @return boolean
     */
    static function inr($needle, $haystack, $strict = false)
    {
        if (is_array($haystack)) {
            if (in_array($needle, $haystack, $strict))
                return true;
            else {
                foreach ($haystack as $v) {
                    if (call_user_func(__METHOD__, $needle, $v, $strict))
                        return true;
                }
            }
        }
    }
}