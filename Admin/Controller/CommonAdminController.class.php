<?php
namespace Admin\Controller;
use Think\Controller;

class CommonAdminController extends \Common\Controller\CommonController {

	public function __construct() {
		parent::__construct();

		if( strtolower( CONTROLLER_NAME ) !== 'login' && LoginController::isLogin() == 0 ){
			$this->error('请先登录','/Admin/Login',3);
			exit;
		}
	}

}