<?php
class AbstractModel extends Utility_ModelAbstract {
	function __construct() {
		Table_AbstractModel::setDefaultAdapter ( self::getMultiDb ()->getDefaultDb () );
	}
}