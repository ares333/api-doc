<?php
class Arrays {
	/**
	 *
	 * @param array $arr
	 * @return boolean
	 */
	static function emptyr($arr) {
		if (empty ( $arr )) {
			return true;
		} else {
			if (is_array ( $arr )) {
				foreach ( $arr as $v ) {
					if (false === call_user_func ( __METHOD__, $v )) {
						return false;
					}
				}
				return true;
			} else {
				return false;
			}
		}
	}
}