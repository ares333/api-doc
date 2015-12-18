<?php
use Ares333\YafLib\Helper\Arrays;
class DocController extends Yaf_Controller_Abstract {
	function init() {
		$out = array ();
		$model = DocModel::getInstance ();
		$out ['nav'] = array ();
		$path = trim ( $this->getRequest ()->getQuery ( 'path', '' ), ' /' );
		$out ['path'] = urlencode ( $path );
		while ( ! empty ( $path ) && $path != '.' ) {
			$out ['nav'] [] = array (
					'name' => basename ( $path ),
					'uri' => '/' . strtolower ( $this->getRequest ()->getControllerName () ) . '?path=' . $path
			);
			$path = dirname ( $path );
		}
		$out ['nav'] = array_reverse ( $out ['nav'] );
		$this->getView ()->assign ( $out );
	}
	function indexAction() {
		$path = trim ( $this->getRequest ()->getQuery ( 'path', '' ), ' /' );
		if ('.txt' == substr ( $path, - 4 )) {
			$this->forward ( 'detail', array (
					'path' => $path
			) );
			return false;
		}
		$out = array ();
		$model = DocModel::getInstance ();
		$out ['list'] = $model->getList ( $path, 4 );
		$mapKey = array ();
		$mapValue = array ();
		Arrays::pregReplaceKeyr ( '/.+/', function ($match) use(&$mapKey) {
			if (0 === strpos ( PHP_OS, 'WIN' )) {
				$enc = mb_convert_encoding ( $match [0], "UTF-8", "GBK" );
			} else {
				$enc = $match [0];
			}
			$mapKey [$enc] = urlencode ( $match [0] );
			return $enc;
		}, $out ['list'] );
		Arrays::pregReplacer ( '/.+/', function ($match) use(&$mapValue) {
			if (0 === strpos ( PHP_OS, 'WIN' )) {
				$enc = mb_convert_encoding ( $match [0], "UTF-8", "GBK" );
			} else {
				$enc = $match [0];
			}
			$mapValue [$enc] = urlencode ( $match [0] );
			return $enc;
		}, $out ['list'] );
		$out ['map'] = $mapKey;
		$out ['mapVal'] = $mapValue;
		$this->getView ()->assign ( $out );
	}
	function detailAction() {
		$out = array ();
		$model = DocModel::getInstance ();
		$path = $this->getRequest ()->getParam ( 'path' );
		//printr ( $path );
		//printr(file_get_contents(APP_PATH.'/PHP/�����/Android��IOS/1.0.0/鐢ㄦ埛.txt'));

		preg_match ( '/\/([\d\.]+(-\w+)?)\//', $path, $match );
		$out ['arr'] = $model->parse ( $path, array (
				'version' => $match [1]
		) );
		$this->getView ()->assign ( $out );
	}
}