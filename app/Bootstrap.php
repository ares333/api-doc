<?php
class Bootstrap extends Yaf_Bootstrap_Abstract {
	function _initPlugin(Yaf_Dispatcher $dispatcher) {
		Yaf_Loader::import ( 'vendor/autoload.php' );
		$dispatcher->registerPlugin ( new Plugin_Init () );
		$dispatcher->registerPlugin ( new Plugin_Smarty () );
		$dispatcher->registerPlugin ( new LoginPlugin () );
	}
}
