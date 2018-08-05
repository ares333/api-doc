<?php
namespace Ares333\Yaf\Helper;

class Http
{

    /**
     *
     * @param array $parse
     * @return string
     */
    static function buildUrl(array $parse)
    {
        $keys = array(
            'scheme',
            'host',
            'port',
            'user',
            'pass',
            'path',
            'query',
            'fragment'
        );
        foreach ($keys as $v) {
            if (! isset($parse[$v])) {
                $parse[$v] = '';
            }
        }
        if ('' !== $parse['scheme']) {
            $parse['scheme'] .= '://';
        }
        if ('' !== $parse['user']) {
            $parse['user'] .= ':';
            $parse['pass'] .= '@';
        }
        if ('' !== $parse['port']) {
            $parse['host'] .= ':';
        }
        if ('' !== $parse['query']) {
            $parse['path'] .= '?';
            // sort
            $query = [];
            parse_str($parse['query'], $query);
            asort($query);
            $parse['query'] = http_build_query($query);
        }
        if ('' !== $parse['fragment']) {
            $parse['query'] .= '#';
        }
        $parse['path'] = preg_replace('/\/+/', '/', $parse['path']);
        return $parse['scheme'] . $parse['user'] . $parse['pass'] .
            $parse['host'] . $parse['port'] . $parse['path'] . $parse['query'] .
            $parse['fragment'];
    }

    /**
     *
     * @return string
     */
    static function getOriginUrl()
    {
        $url = static::getCurrentUrl();
        if (! isset($url)) {
            return;
        }
        $url = parse_url($url);
        $url = array(
            'scheme' => $url['scheme'],
            'host' => $url['host']
        );
        return static::buildUrl($url);
    }

    /**
     *
     * @return void|string
     */
    static function getCurrentUrl()
    {
        if (PHP_SAPI == 'cli') {
            return;
        }
        $server = $_SERVER;
        if (! empty($server['REQUEST_SCHEME'])) {
            $scheme = $server['REQUEST_SCHEME'];
        } else {
            $isSecure = false;
            if (isset($server['HTTPS']) && $server['HTTPS'] !== 'off') {
                $isSecure = true;
            } elseif (! empty($server['HTTP_X_FORWARDED_PROTO']) &&
                $server['HTTP_X_FORWARDED_PROTO'] == 'https' || ! empty(
                    $server['HTTP_X_FORWARDED_SSL']) &&
                $server['HTTP_X_FORWARDED_SSL'] == 'on') {
                $isSecure = true;
            }
            $scheme = $isSecure ? 'https' : 'http';
        }
        $url = $scheme . '://' . $server['HTTP_HOST'] . $server['REQUEST_URI'];
        return $url;
    }

    /**
     *
     * @return string
     */
    static function getClientIp()
    {
        if (PHP_SAPI === 'cli') {} else {
            $unknown = 'unknown';
            $ip = '';
            $server = $_SERVER;
            if (isset($server['HTTP_X_FORWARDED_FOR']) &&
                $server['HTTP_X_FORWARDED_FOR'] &&
                strcasecmp($server['HTTP_X_FORWARDED_FOR'], $unknown)) {
                $ip = $server['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($server['REMOTE_ADDR']) && $server['REMOTE_ADDR'] &&
                strcasecmp($server['REMOTE_ADDR'], $unknown)) {
                $ip = $server['REMOTE_ADDR'];
            }
            if (false !== strpos($ip, ','))
                $ip = reset(explode(',', $ip));
            return $ip;
        }
    }

    /**
     * is mobile device
     *
     * @return bool
     */
    static function isMobile()
    {
        $server = $_SERVER;
        // must be wap if contains HTTP_X_WAP_PROFILE
        if (isset($server['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // must be wap if via contains 'wap',some telecom will block this
        if (isset($server['HTTP_VIA'])) {
            return stristr($server['HTTP_VIA'], "wap") ? true : false;
        }
        // not exact
        if (isset($server['HTTP_USER_AGENT'])) {
            $clientkeywords = array(
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i",
                strtolower($server['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        // not exact
        if (isset($server['HTTP_ACCEPT'])) {
            if ((strpos($server['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos(
                $server['HTTP_ACCEPT'], 'text/html') === false || (strpos(
                $server['HTTP_ACCEPT'], 'vnd.wap.wml') <
                strpos($server['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }
}