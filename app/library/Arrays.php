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

	/**
	 *
	 * @param array $arr
	 * @param mixed $key
	 */
	static function current($arr, $key) {
		if (! is_array ( $key )) {
			$key = array (
					$key
			);
		}
		foreach ( $key as $v ) {
			if (array_key_exists ( $v, $arr )) {
				$arr = $arr [$v];
			}
		}
		return $arr;
	}
}