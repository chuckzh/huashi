<?php

/**
 * 获取raw请的数据
 * @return [type] [description]
 */
function get_raw_input(){
	$data = file_get_contents( "php://input ");
	if( !empty( $data )){
		return json_decode($data, true);
	}else{
		return false;
	}
}

/**
 * 服务器端的返回json格式的数据
 * @param  [type] $data [对象或数组的混合数据]
 * @return [type]      [多维数组]
 */
function make_json_response( $data ){
	// header("Content-Type:text/json;charset=utf-8");

	// data_to_arr($data);

	// echo json_encode($data);
	$js = new \Lib\MyUtil\Services_JSON();
	echo $js->encode( $data );
	exit;
}

/**
 * 按一定格式的输出json格式
 * @param  [type] $data [description]
 * @param  [type] $code [description]
 * @param  string $msg  [description]
 * @return [type]       [description]
 */
function make_general_response($data, $code, $msg = ''){
	$arr = array('content' => $data,
				'code' => $code,
				'msg' => $msg,
				);
	make_json_response($arr);
}

/**
 * 递归处理数组和对象，并转化为多维数组
 * @param  [mix] &$input [要转化为多维数组]
 */
function data_to_arr(&$input) {
	if (is_string($input)) {
		$input = urldecode($input);
	} else if (is_array($input)) {
		foreach ($input as &$value) {
			data_to_arr($value);
		}
	 	unset($value);
	} else if (is_object($input)) {
		$vars = array_keys(get_object_vars($input));
		$obj2arr = array();

		foreach ($vars as $var) {
			$obj2arr[$var] = data_to_arr($input -> $var);
		}

		$input = $obj2arr;
	}
}

//排序
function file_cmp_func($a, $b) {
	$order = I('put.order', '', 'trim');
	if ($a['is_dir'] && !$b['is_dir']) {
		return -1;
	} else if (!$a['is_dir'] && $b['is_dir']) {
		return 1;
	} else {
		if ($order == 'size') {
			if ($a['filesize'] > $b['filesize']) {
				return 1;
			} else if ($a['filesize'] < $b['filesize']) {
				return -1;
			} else {
				return 0;
			}
		} else if ($order == 'type') {
			return strcmp($a['filetype'], $b['filetype']);
		} else {
			return strcmp($a['filename'], $b['filename']);
		}
	}
}
?>