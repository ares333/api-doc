<?php
namespace Ares333\Yaf\Tool;

/**
 * php.ini
 * auto_prepend_file = xhprof.php
 * xhprof.output_dir = /path/to/xhprof/run
 *
 * CLI:
 * yum install -y graphviz
 *
 * UI:
 * https://github.com/phacility/xhprof
 *
 * <php7.0.0
 * https://pecl.php.net/package/xhprof
 *
 * >=php7.0.0
 * https://github.com/tideways/php-profiler-extension (https://tideways.io/profiler/xhprof-for-php7-php5.6)
 */
class XHProf
{

    protected $path;

    protected $domain;

    /**
     *
     * @param string $path
     * @param string $domain
     * @param bool $auto
     */
    function __construct($path, $domain, $auto = null)
    {
        if (! isset($auto)) {
            $auto = false;
        }
        $this->path = $path;
        $this->domain = $domain;
        if (true === $auto) {
            register_shutdown_function(
                function () {
                    $url = $this->run();
                    $isHtml = false;
                    foreach (headers_list() as $v) {
                        $match=[];
                        if (preg_match('/^Content-type: (.+?) /i', $v, $match) &&
                             false !== strpos($match[0], 'html')) {
                            $isHtml = true;
                            break;
                        }
                    }
                    if ($isHtml) {
                        $link = "<br>\n<br>\n<a href=\"{$url}\" target=\"blank\">xhprof</a>\n";
                    } else {
                        $link = "\n\n$url";
                    }
                    echo $link;
                });
        }
        $this->call('enable');
    }

    protected function call($name)
    {
        $args = array();
        if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
            $functionPre = 'tideways';
            if ($name === 'enable') {
                $args[] = TIDEWAYS_FLAGS_NO_SPANS;
            }
        } else {
            $functionPre = 'xhprof';
        }
        return call_user_func_array($functionPre . '_' . $name, $args);
    }

    function run()
    {
        $xhprofData = $this->call('disable');
        include_once $this->path . "/xhprof_lib/utils/xhprof_lib.php";
        include_once $this->path . "/xhprof_lib/utils/xhprof_runs.php";
        $name = 'XHProfRuns_Default';
        $outputDir = $this->path . '/run';
        $xhprofRuns = new $name($outputDir);
        $type = dechex(crc32($this->path));
        $run_id = $xhprofRuns->save_run($xhprofData, $type);
        $url = "http://{$this->domain}/index.php?run={$run_id}&source={$type}";
        return $url;
    }
}