<?php
use Ares333\YafLib\Plugin\Init;
use Ares333\YafLib\Plugin\Smarty;
use Yaf\Bootstrap_Abstract;
use Yaf\Dispatcher;
use Yaf\Loader;
class Bootstrap extends Bootstrap_Abstract {
	function _initPlugin(Dispatcher $dispatcher) {
		Loader::import ( 'vendor/autoload.php' );
		$dispatcher->registerPlugin ( new Init () );
		$dispatcher->registerPlugin ( new Smarty () );
		$dispatcher->registerPlugin ( new LoginPlugin () );
	}
}