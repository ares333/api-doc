<?php
class ApiController extends Yaf_Controller_Abstract {
	function testAction() {
		ApiModel::getInstance ()->test ();
		return false;
	}
}