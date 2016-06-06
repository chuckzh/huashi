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
        $pwd_crypt = \Think\Crypt::encrypt(urlencode('123456'), C('MY_DATA_KEY'));
        make_general_response($pwd_crypt, '0');
    	if( IS_AJAX ){

    		$name = I('put.user_name');
    		$pwd = I('put.user_password');

            $pwd_crypt = \Think\Crypt::encrypt($pwd, C('MY_DATA_KEY'));

            $map = array( 'user_name' => $name );
            $field = array('user_password,user_id,last_ip,last_login,actions');
            $cookie_arr = $user_db = M('AdminUser')->where( $map )->field( $field )->find();
            if( $user_db['user_password'] == $pwd_crypt ){

                $cookie_arr['user_name'] = $name;
                unset( $cookie_arr['actions'] );
                unset( $cookie_arr['user_password'] );

                $user_cookie = \Think\Crypt::encrypt(implode(',', $cookie_arr), C('MY_COOKIE_KEY'));//cookie加密

                $action_session = \Think\Crypt::encrypt($user_db['actions'], C('MY_COOKIE_KEY'));//session加密

                cookie("sd_admin_user", $user_cookie);//存储user_name,user_id,last_ip,last_login 到 cookie
                session('sd_admin_action', $action_session);//存储到action列表到 session

                make_general_response('', '0');

            }else if( $user_db['user_password'] == $pwd_crypt ){

                make_general_response('', '-1', '密码不正确');

            }else if( empty($user_db) ){

                make_general_response('', '-1', '用户不存在');

            }
    	}
    	exit;

    }

    /**
     * 是否登录
     * @return int 0:未登录 >0:已登录
     */
    public static function isLogin(){
        $user_cookie = \Think\Crypt::decrypt( cookie('sd_admin_user'), C('MY_COOKIE_KEY') );//获取解密数据
        $user_info = array();
        if($user_cookie){
            $user_info = explode(',', $user_cookie);
        }

        if( iseet( $user_info['user_id'] ) ){
            return $user_info['user_id'];
        }
        return 0;
    }

    /**
     * 用户登出
     */
    public function logout(){
        $user_cookie = \Think\Crypt::decrypt( cookie('sd_admin_user'), C('MY_COOKIE_KEY') );//获取解密数据
        $user_info = aray();
        if($user_cookie){
            $user_info = explode(',', $user_cookie);
        }
        if( !empty( $user_info ) ){
            cookie('sd_admin_user', null);
            session('sd_admin_action', null);
        }

        make_general_response('', '-1', '退出成功');

    }
}