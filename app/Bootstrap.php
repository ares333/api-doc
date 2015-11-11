<?php
class Bootstrap extends Yaf_Bootstrap_Abstract {
	function _initPlugin(Yaf_Dispatcher $dispatcher) {
		$dispatcher->registerPlugin ( new Plugin_Init () );
		$dispatcher->registerPlugin ( new Plugin_Smarty () );
		$dispatcher->registerPlugin ( new LoginPlugin () );
	}
}
