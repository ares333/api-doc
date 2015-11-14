<?php
class ApiModel extends AbstractModel {
	function test() {
		$request = Yaf_Dispatcher::getInstance ()->getRequest ();
		$type = key ( $request->getParams () );
		$params = array ();
		$params ['request'] = $request->getRequest ();
		$params ['query'] = $request->getQuery ();
		$params ['post'] = $request->getPost ();
		$params ['input'] = file_get_contents ( 'php://input' );
		$isJson = false;
		if (array_key_exists ( '_json', $params ['query'] )) {
			$isJson = true;
			unset ( $params ['query'] ['_json'], $params ['request'] ['_json'] );
		}
		if ($type == 'param') {
			printr ( $params, true, false, true );
		} elseif ($type == 'curl') {
			$str = 'curl';
			if (! empty ( $params ['post'] )) {
				if (! empty ( $params ['query'] ['json'] ) && $params ['query'] ['json'] == 'yes') {
					$str .= ' -X POST -d ' . escapeshellarg ( $params ['input'] );
				} else {
					$str .= ' -X POST -d ' . escapeshellarg ( http_build_query ( $params ['post'] ) );
				}
			}
			if (! empty ( $params ['query'] )) {
				$qstr = '';
				if (! empty ( $params ['query'] ['_url'] )) {
					$qstr .= $params ['query'] ['_url'] . '?';
					unset ( $params ['query'] ['_url'], $params ['request'] ['_url'] );
				} else {
					$qstr .= '?';
				}
				$qstr .= http_build_query ( $params ['query'] );
				$str .= ' ' . escapeshellarg ( $qstr );
			}
			if ($isJson) {
				$str = json_encode ( $str );
			}
			echo $str;
		} else {
			echo 'api error, contact the administrator';
		}
	}
}