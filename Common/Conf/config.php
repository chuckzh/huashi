<?php
return array(

	'DEFAULT_MODULE'     => 'Home', //默认模块
	'URL_MODEL'          => '2', //URL模式
	'SESSION_AUTO_START' => true, //是否开启session
	'LOAD_EXT_CONFIG'    => 'db',
	// 'TMPL_ENGINE_TYPE' =>'think',
	'TMPL_FILE_DEPR'=>'_',

	'MODULE_ALLOW_LIST'    =>    array('Home','Admin'),
	'DEFAULT_MODULE'       =>    'Home',

	'TMPL_PARSE_STRING'=>array(
		'__STYLE__'=> '/Home/Style',
		'__PUBLIC__'=> '/Admin/Public',
		'__TMPL__' => MODULE_PATH.'View/',
	),

	/*'TMPL_L_DELIM'=>'{:',
	'TMPL_R_DELIM'=>':}',
	*/
	'AUTOLOAD_NAMESPACE' => array(
		'Lib'  => APP_PATH.'Lib',
	),

	'DATA_CRYPT_TYPE' => 'Base64',
	'MY_DATA_KEY' => '6KxkpH6bZX$iR^TQ',
	'COOKIE_KEY' => 'a#4Z&LONL49V4nmm',
	'VERIFY_CODE_CFG' => array(
			'seKey' =>'jS&bu44#*8pHdHnf',
			'length' => 5,
			'fontSize' => 24,
	),

	'UPLOAD_ROOT' => 'data/'

);