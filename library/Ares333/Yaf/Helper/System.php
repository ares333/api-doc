<?php
namespace Ares333\Yaf\Helper;

class System
{

    /**
     * sub process will not exit,so no more codes should be executed after this method
     *
     * @param callback $callback
     *            main process code to be executed
     * @param callback $callbackSub
     *            in sub process code to be executed
     */
    static function fork($callback, $callbackSub, $num = null)
    {
        if (0 === strpos(PHP_OS, 'WIN')) {
            user_error('Windows is not supported', E_USER_ERROR);
        }
        if (! isset($num)) {
            $num = static::cpuNum();
        }
        $pidList = array();
        for ($i = 0; $i < $num; $i ++) {
            $pid = pcntl_fork();
            $pidList[] = $pid;
            if ($pid == - 1) {
                user_error("could not fork", E_USER_ERROR);
            } else if ($pid) {
                if ($i == $num - 1) {
                    if (isset($callback)) {
                        call_user_func($callback, $pidList);
                    }
                    foreach ($pidList as $v) {
                        pcntl_waitpid($v);
                    }
                }
            } else {
                call_user_func($callbackSub);
                break;
            }
        }
    }

    /**
     * Returns the number of available CPU cores
     *
     * Should work for Linux, Windows, Mac & BSD
     *
     * @return integer|null
     */
    static function cpuNum()
    {
        if (0 === strpos(PHP_OS, 'WIN')) {
            user_error('Windows not supported', E_USER_ERROR);
        }
        $numCpus = null;
        if (is_file('/proc/cpuinfo')) {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            $matches = [];
            preg_match_all('/^processor/m', $cpuinfo, $matches);
            $numCpus = count($matches[0]);
        } else if ('WIN' == strtoupper(substr(PHP_OS, 0, 3))) {
            $process = popen('wmic cpu get NumberOfCores', 'rb');
            if (false !== $process) {
                fgets($process);
                $numCpus = intval(fgets($process));
                pclose($process);
            }
        } else {
            $process = popen('sysctl -a', 'rb');
            if (false !== $process) {
                $output = stream_get_contents($process);
                $matches = [];
                preg_match('/hw.ncpu: (\d+)/', $output, $matches);
                if ($matches) {
                    $numCpus = intval($matches[1][0]);
                }
                pclose($process);
            }
        }

        return $numCpus;
    }
}