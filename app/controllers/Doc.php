<?php
class DocController extends Yaf_Controller_Abstract {
	function init() {
		$out = array ();
		$model = DocModel::getInstance ();
		$out ['nav'] = array ();
		$out ['path'] = $path = trim ( $this->getRequest ()->getQuery ( 'path', '' ), ' /' );
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
		$this->getView ()->assign ( $out );
	}
	function detailAction() {
		$out = array ();
		$model = DocModel::getInstance ();
		$path = $this->getRequest ()->getParam ( 'path' );
		preg_match ( '/\/([\d\.]+(-\w+)?)\.txt/', $path, $match );
		$out ['arr'] = $model->parse ( $path, array (
				'version' => $match [1]
		) );
		$this->getView ()->assign ( $out );
	}
}