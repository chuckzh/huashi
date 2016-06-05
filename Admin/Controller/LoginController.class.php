<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {

	public function index(){
		$this->display();
	}

	public function veirfyImg(){
		$Verify = new \Think\Verify();
		$Verify->entry();
	}

	//验证密码
    public function verify(){

    	if( IS_AJAX ){
    		$pwd_crypt = \Think\Crypt::encrypt(C('MY_DATA_KEY'));

    		$name = I('post.user_name');
    		$pwd = I('post.user_password');

    		if(  )
    	}
    	exit;

    }

    public static function isLogin(){

    }
}