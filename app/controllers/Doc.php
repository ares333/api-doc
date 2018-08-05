<?php
use Yaf\Controller_Abstract;

class DocController extends Controller_Abstract
{

    function init()
    {
        $out = array();
        $out['nav'] = array();
        $path = trim($this->getRequest()->getQuery('path', ''), ' /');
        $out['path'] = urlencode($path);
        while (! empty($path) && $path != '.') {
            $name = explode('/', $path);
            $name = end($name);
            if (0 === strpos(PHP_OS, 'WIN')) {
                $name = mb_convert_encoding($name, "UTF-8", "GBK");
            }
            $out['nav'][] = array(
                'name' => $name,
                'uri' => '/' .
                strtolower($this->getRequest()->getControllerName()) . '?path=' .
                $path
            );
            $path = dirname($path);
        }
        $out['nav'] = array_reverse($out['nav']);
        $this->getView()->assign($out);
    }

    function indexAction()
    {
        $path = trim($this->getRequest()->getQuery('path', ''), ' /');
        if ('.txt' == substr($path, - 4)) {
            $this->forward('detail', array(
                'path' => $path
            ));
            return false;
        }
        $out = array();
        $model = DocModel::getInstance();
        $out['list'] = $model->getList($path, 3);
        // set version list in inverted order
        $sort = function (&$v) {
            if (is_array($v)) {
                reset($v);
                $k1 = key($v);
                $v1 = current($v);
                next($v);
                $k2 = key($v);
                $v2 = current($v);
                $pattern = '/^v\d\.\d\.\d/';
                if (preg_match($pattern, $k1) && preg_match($pattern, $k2)) {
                    uksort($v, function ($a, $b) {
                        return $a < $b;
                    });
                }
                if (is_string($v1) && is_string($v2)) {
                    if (preg_match($pattern, $v1) && preg_match($pattern, $v2)) {
                        uasort($v, function ($a, $b) {
                            return $a < $b;
                        });
                    }
                }
            }
        };
        $sort($out['list']);
        Arrays::walkr($out['list'], function (&$v) use ($sort) {
            $sort($v);
        });
        $mapKey = array();
        $mapValue = array();
        Arrays::pregReplaceKeyr('/.+/',
            function ($match) use (&$mapKey) {
                if (0 === strpos(PHP_OS, 'WIN')) {
                    $enc = mb_convert_encoding($match[0], "UTF-8", "GBK");
                } else {
                    $enc = $match[0];
                }
                $mapKey[$enc] = urlencode($match[0]);
                return $enc;
            }, $out['list']);
        Arrays::pregReplacer('/.+/',
            function ($match) use (&$mapValue) {
                if (0 === strpos(PHP_OS, 'WIN')) {
                    $enc = mb_convert_encoding($match[0], "UTF-8", "GBK");
                } else {
                    $enc = $match[0];
                }
                $mapValue[$enc] = urlencode($match[0]);
                return $enc;
            }, $out['list']);
        $out['map'] = $mapKey;
        $out['mapVal'] = $mapValue;
        $this->getView()->assign($out);
    }

    function detailAction()
    {
        $out = array();
        $model = DocModel::getInstance();
        $path = $this->getRequest()->getParam('path');
        // v1.0.0-beta
        $match = [];
        preg_match('/\/(v[\d\.]+(-\w+)?|trunk)/', $path, $match);
        $arr = array();
        if (isset($match[1])) {
            $arr['version'] = $match[1];
        }
        $out['arr'] = $model->parse($path, $arr);
        $this->getView()
            ->getAdapter()
            ->registerClass("Arrays", 'Arrays');
        $this->getView()->assign($out);
    }
}