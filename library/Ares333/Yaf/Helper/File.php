<?php
namespace Ares333\Yaf\Helper;

class File
{

    /**
     * remove dir recursively
     *
     * @param string $dir
     *            absolute path is highly recommanded
     * @return boolean
     */
    static function rmdir($dir)
    {
        clearstatcache();
        if (! is_dir($dir)) {
            return false;
        }
        // glob has someproblems with dot started filename,so use scandir
        $files = scandir($dir);
        foreach ($files as $k => $v) {
            if ($v == '.' || $v == '..') {
                unset($files[$k]);
            } else {
                $files[$k] = $dir . DIRECTORY_SEPARATOR . $v;
            }
        }
        while (! empty($files)) {
            $file = array_pop($files);
            if (is_file($file)) {
                unlink($file);
            } else {
                $subFiles = scandir($file);
                if (count($subFiles) == 2) {
                    rmdir($file);
                } else {
                    foreach ($subFiles as $k => $v) {
                        if ($v == '.' || $v == '..') {
                            unset($subFiles[$k]);
                        } else {
                            $subFiles[$k] = $file . DIRECTORY_SEPARATOR . $v;
                        }
                    }
                    $files = array_merge($files,
                        array(
                            $file
                        ), $subFiles);
                }
            }
        }
        return rmdir($dir);
    }

    /**
     * is an absolute path
     *
     * @param string $path
     */
    static function isAbsolute($path)
    {
        if (0 === strpos(PHP_OS, 'Linux')) {
            return 0 === strpos($path, '/');
        } elseif (0 === strpos(PHP_OS, 'WIN')) {
            $path = strtolower($path);
            return (bool) preg_match('/[a-z]+:(\/|\\\\)/i', $path);
        } else {
            user_error('Unknown OS', E_USER_ERROR);
        }
    }

    /**
     * get last n line of the file
     *
     * @param string $filename
     * @param integer $n
     * @return string
     */
    static function tail($file, $n = null)
    {
        if (! isset($n)) {
            $n = 8;
        }
        if (! $fp = fopen($file, 'r')) {
            user_error('failed open file, file=' . $file, E_USER_WARNING);
            return;
        }
        $pos = - 2;
        $eof = "";
        $str = "";
        while ($n > 0) {
            while ($eof != "\n") {
                if (! fseek($fp, $pos, SEEK_END)) {
                    $eof = fgetc($fp);
                    $pos --;
                } else {
                    break;
                }
            }
            $str .= fgets($fp);
            $eof = "";
            $n --;
        }
        return $str;
    }

    /**
     * synchronized by file lock
     *
     * @param string $lockFile
     * @param mixed $callback
     * @param array $param
     * @param bool $block
     */
    static function synchronized($lockFile, $callback, $param = array(), $block = false)
    {
        if (PHP_OS != 'Linux') {
            user_error(__METHOD__ . ' can only run in Linux', E_USER_ERROR);
        }
        clearstatcache(true, $lockFile);
        $f = fopen($lockFile, 'c');
        if (false === $f) {
            user_error('Open file failed, file=' . $lockFile, E_USER_ERROR);
        }
        $lock = LOCK_EX;
        if (! $block) {
            $lock |= LOCK_NB;
        }
        if (flock($f, $lock)) {
            $res = call_user_func_array($callback, $param);
        }
        flock($f, LOCK_UN);
        fclose($f);
        return $res;
    }

    /**
     *
     * @param string $dir
     * @return array
     */
    static function tree($dir, $maxDepth = null, $ignore = array())
    {
        static $depth = 0;
        $res = array();
        if (isset($maxDepth) && $depth >= $maxDepth) {
            return $res;
        }
        if (is_dir($dir)) {
            $dirs = static::scandir($dir, 'dir', 1, $ignore);
            foreach ($dirs as $v) {
                $depth ++;
                $res[$v] = call_user_func(__METHOD__, $dir . '/' . $v,
                    $maxDepth, $ignore);
                $depth --;
            }
            $res = array_merge($res, static::scandir($dir, 'file', 1, $ignore));
        }
        return $res;
    }

    /**
     * scan dir recersively
     *
     * @param string $dir
     * @param string $mode
     *            all
     *            file
     *            dir
     * @param integer $depth
     *            null is infinite
     * @param array|string $ignore
     *            wildcard
     * @param number $order
     *            asc
     *            desc
     * @param resource $context
     *            see php menual on scandir
     * @return array
     */
    static function scandir($dir, $mode = null, $depth = null, $ignore = null, $order = null,
        $context = null)
    {
        if (! isset($mode)) {
            $mode = 'all';
        }
        if (! isset($order)) {
            $order = 'asc';
        }
        static $modes = array(
            'file',
            'dir',
            'all'
        );
        $r = array();
        if (! in_array($mode, $modes)) {
            user_error('mode is invalid', E_USER_WARNING);
            return;
        }
        if (is_numeric($depth) && -- $depth < 0)
            return $r;
        $dir = rtrim($dir, '/');
        if (! is_dir($dir)) {
            return $r;
        }
        if ($order == 'asc') {
            $orderInt = 0;
        } elseif ($order == 'desc') {
            $orderInt = 1;
        } else {
            user_error('order type is invalid, order=' . $order, E_USER_WARNING);
            return;
        }
        if (is_resource($context)) {
            $list = scandir($dir, $orderInt, $context);
        } else {
            $list = scandir($dir, $orderInt);
        }
        if (is_array($list) and ! empty($list)) {
            foreach ($list as $v) {
                if ($v == '.' || $v == '..') {
                    continue;
                } else {
                    if (! empty($ignore)) {
                        if (is_string($ignore)) {
                            $ignore = array(
                                $ignore
                            );
                        }
                        foreach ($ignore as $v1) {
                            if (fnmatch($v1, $v)) {
                                continue 2;
                            }
                        }
                    }
                    if (is_file($dir . '/' . $v) and
                         ($mode == 'file' or $mode == 'all')) {
                        $r[] = $v;
                    } elseif (is_dir($dir . '/' . $v)) {
                        if ($mode == 'dir' or $mode == 'all')
                            $r[] = $v;
                        $t = static::scandir($dir . '/' . $v, $mode, $depth,
                            $ignore, $order, $context);
                        if (! empty($t)) {
                            foreach ($t as $k1 => $v1) {
                                $t[$k1] = $v . '/' . $v1;
                            }
                        }
                        $r = array_merge($r, $t);
                    }
                }
            }
        } else {
            $r = $list;
        }
        return $r;
    }
}