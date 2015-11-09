<?php
class IndexController extends Yaf_Controller_Abstract {
	function indexAction() {
		$this->redirect ( '/doc' );
		return false;
	}
}