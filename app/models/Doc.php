<?php
use Ares333\Helper\File;
use Ares333\Helper\Arrays;
class DocModel extends AbstractModel {
	private $basePath;
	function __construct() {
		$this->basePath = APP_PATH . '/data/doc/api';
	}
	function getList($path, $depth = null) {
		$dir = $this->basePath . '/' . $path;
		$list = File::tree ( $dir, $depth, array (
				'.svn'
		) );
		Arrays::unsetByValuer ( $list, array (
				'_meta.txt'
		) );
		Arrays::unsetByKey ( $list, array (
				'_inc'
		) );
		return $list;
	}
	function getApiList($path) {
		$file = $this->basePath . '/' . ltrim ( $path, '/' );
		$res = $this->getContent ( $file );
		$res = array_keys ( $this->parseArr ( $res, 1 ) );
		Arrays::unsetByValuer ( $res, '_meta' );
		return $res;
	}
	private function getContent($file) {
		if (! file_exists ( $file )) {
			return;
		}
		$str = file_get_contents ( $file );
		if (0 === strpos ( $str, chr ( 239 ) . chr ( 187 ) . chr ( 191 ) )) {
			$str = substr ( $str, 3 );
		}
		$content = str_replace ( "\r", '', trim ( $str ) );
		return $content;
	}
	function parse($path, array $var = array(), $maxDepth = null) {
		$file = $this->basePath . '/' . ltrim ( $path, '/' );
		$meta = array ();
		foreach ( array (
				dirname ( dirname ( $file ) ),
				dirname ( $file )
		) as $v ) {
			$v .= '/_meta.txt';
			if (file_exists ( $v )) {
				$meta = array_merge_recursive ( $meta, $this->parseArr ( $this->getContent ( $v ), $maxDepth ) );
			}
		}
		$content = $this->getContent ( $file );
		// parse here doc
		$hereDoc = array ();
		$content = preg_replace_callback ( '/<<<(.+)>>>/sm', function (&$node) use(&$hereDoc) {
			$hereDoc [] = $node [1];
			return '\d' . (count ( $hereDoc ) - 1);
		}, $content );
		$arr = $this->parseArr ( $content, $maxDepth );
		if (array_key_exists ( '_meta', $arr )) {
			if (! empty ( $arr ['_meta'] )) {
				$meta = array_merge_recursive ( $meta, $arr ['_meta'] );
			}
			unset ( $arr ['_meta'] );
		}
		if (! empty ( $meta ['_unset'] )) {
			Arrays::unsetr ( $meta, $meta ['_unset'] );
			unset ( $meta ['_unset'] );
		}
		foreach ( $arr as $k => $v ) {
			if (null == $v) {
				unset ( $arr [$k] );
				continue;
			}
			$parent = $meta;
			// unset
			if (! empty ( $v ['_unset'] )) {
				Arrays::unsetr ( $parent, $v ['_unset'] );
				unset ( $v ['_unset'] );
			}
			// default
			if (! empty ( $parent ['default'] )) {
				list ( $v, $temp ) = array (
						$parent ['default'],
						$v
				);
				Arrays::merger ( $v, $temp );
			}
			// replace
			Arrays::pregReplacer ( '/^\\\k$/', $k, $v );
			// key clean
			Arrays::pregReplaceKeyr ( '/^\\\s(\d+)$/', '\\1', $v );
			// prefix
			if (! empty ( $parent ['prefix'] )) {
				Arrays::prefixr ( $v, $parent ['prefix'] );
			}
			// suffix
			if (! empty ( $parent ['suffix'] )) {
				Arrays::suffixr ( $v, $parent ['suffix'] );
			}
			// wraper
			if (! empty ( $parent ['wraper'] )) {
				Arrays::wraperr ( $v, $parent ['wraper'] );
			}
			// var replace
			if (! empty ( $var )) {
				array_walk_recursive ( $v, function (&$v, $k, $var) {
					if (is_string ( $v )) {
						preg_match_all ( '/\{\$(\w+)\}/', $v, $matches );
						if (! empty ( $matches [1] )) {
							foreach ( $matches [1] as $v1 ) {
								if (isset ( $var [$v1] )) {
									$v = str_replace ( '{$' . $v1 . '}', $var [$v1], $v );
								}
							}
						}
					}
				}, $var );
			}
			// \d recover
			$search = $replace = array ();
			for($i = 0; $i < count ( $hereDoc ); $i ++) {
				$search [] = '/^\\\d' . $i . '$/';
				$replace [] = $hereDoc [$i];
			}
			Arrays::pregReplacer ( $search, $replace, $v );
			$arr [$k] = $v;
		}
		// \h self inherit
		$funcH = function (array &$subject) use(&$funcH, $arr) {
			foreach ( $subject as $k => &$v ) {
				if ('\h' === $k) {
					$key = explode ( ' ', $v );
					$value = \Arrays::current ( $arr, $key );
					$key = array_pop ( $key );
					$subject [$key] = $value;
					unset ( $subject [$k] );
				} elseif (is_array ( $v )) {
					call_user_func_array ( $funcH, array (
							&$v
					) );
				}
			}
		};
		$funcH ( $arr );
		// \i include
		$dir = dirname ( $file ) . '/_inc';
		Arrays::pregReplaceCallbackr ( '/\\\i(.+)/', function ($node) use($dir) {
			$file = $dir . '/' . trim ( $node [1] );
			if (file_exists ( $file )) {
				return file_get_contents ( $file );
			}
		}, $arr );
		return $arr;
	}
	private function parseArr($str, $maxDepth = null) {
		static $depth = 0;
		$res = array ();
		if (isset ( $maxDepth ) && $depth >= $maxDepth) {
			return $res;
		}
		$arr = preg_split ( '/\n+(?=^[^\t])/um', $str );
		// 解析顶头的组
		foreach ( $arr as $v ) {
			$two = explode ( "\n", $v, 2 );
			if (false === strpos ( $two [0], "\t" )) {
				if (isset ( $two [1] )) {
					$depth ++;
					$res [$two [0]] = $this->parseArr ( preg_replace ( '/^\t/m', '', $two [1] ), $maxDepth );
					$depth --;
				} else {
					if (0 === strpos ( $two [0], '[]' )) {
						if ('[]' == $two [0]) {
							$res [] = array ();
						} else {
							$res [] = substr ( $two [0], 2 );
						}
					} else {
						$res [$two [0]] = null;
					}
				}
			} else {
				$two = preg_split ( "/\t+/", $two [0] );
				// 第一列是否作为键
				if (0 !== strpos ( $two [0], '[]' )) {
					$key = $two [0];
				} else {
					$key = null;
					$two [0] = substr ( $two [0], 2 );
				}
				// 是否只有两列
				if (2 < count ( $two )) {
					if (isset ( $key )) {
						array_shift ( $two );
					}
				} else {
					if (isset ( $key )) {
						$two = $two [1];
					}
				}
				// 附加
				if (! isset ( $key )) {
					$res [] = $two;
				} else {
					$res [$key] = $two;
				}
			}
		}
		return $res;
	}
}