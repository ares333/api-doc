<?php
use Ares333\Plugin\Init;
use Ares333\Plugin\Smarty;
class Bootstrap extends Yaf_Bootstrap_Abstract {
	function _initPlugin(Yaf_Dispatcher $dispatcher) {
		Yaf_Loader::import ( 'vendor/autoload.php' );
		$dispatcher->registerPlugin ( new Init () );
		$dispatcher->registerPlugin ( new Smarty () );
		$dispatcher->registerPlugin ( new LoginPlugin () );
	}
}
