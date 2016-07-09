<?php
use Yaf\Bootstrap_Abstract;
use Yaf\Dispatcher;
use Yaf\Loader;
use Ares333\YafLib\Plugin\PHPConfig;
use Ares333\YafLib\Plugin\Module;
use Ares333\YafLib\SmartyView;
class Bootstrap extends Bootstrap_Abstract {
	function _initPlugin(Dispatcher $dispatcher) {
		Loader::import ( 'vendor/autoload.php' );
		$dispatcher->registerPlugin ( new PHPConfig () );
		$dispatcher->registerPlugin ( new LoginPlugin () );
		$dispatcher->registerPlugin ( new Module ( array (
				Module::TYPE_CLI => array (
						'enable' => true
				)
		) ) );
	}
	function _initView(Dispatcher $dispatcher) {
		$view = new SmartyView ();
		$dispatcher->setView ( $view );
	}
}