<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends CommonAdminController {



	public function index(){
		$this->display();
	}

	public function veirfyImg(){
		$Verify = new \Think\Verify( $this->_verifyCodeCfg );
		$Verify->entry();
	}

	//验证密码
    public function verify(){

    	if( IS_AJAX ){

    		$name = I('put.user_name');
            $pwd = I('put.user_password');
    		$verify_code = I('put.verify_code');

            if( empty( $name ) || empty( $pwd ) || empty( $verify_code ) ){
                make_general_response('', '-7', '请填写完整数据');
            }

            $Verify = new \Think\Verify( $this->_verifyCodeCfg );
            if( !$Verify->check( $verify_code ) ){
                make_general_response('', '-1', '验证码不正确');
            }

            $map = array( 'user_name' => $name );
            $field = array('user_password, salt, id AS user_id,last_ip,last_login,actions');
            $cookie_arr = $user_db = M('AdminUser')->where( $map )->field( $field )->find();

            $pwd_crypt = crypt($pwd, C('MY_DATA_KEY') . $user_db['salt']);

            if( empty($user_db) ){

                make_general_response('', '-2', '用户不存在');

            }else if( $user_db['user_password'] == $pwd_crypt ){

                $cookie_arr['user_name'] = $name;
                unset( $cookie_arr['actions'] );
                unset( $cookie_arr['user_password'] );
                unset( $cookie_arr['salt'] );

                $user_cookie = \Think\Crypt::encrypt( json_encode( $cookie_arr ), C('MY_COOKIE_KEY'));//cookie加密

                $action_session = $user_db['actions'];//session加密

                cookie("sd_admin_user", $user_cookie);//存储user_name,user_id,last_ip,last_login 到 cookie
                session('sd_admin_action', $action_session);//存储到action列表到 session

                //记录登录信息
                $user_up = array();
                $user_up['salt'] = rand_str();
                $user_up['last_login'] = time();
                $user_up['last_ip'] = get_client_ip();
                $user_up['id'] = $user_db['user_id'];
                M('AdminUser')->save( $user_up );

                make_general_response('', '0','登录成功');

            }else if( $user_db['user_password'] != $pwd_crypt ){

                make_general_response('', '-3', '密码不正确');

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
            $user_info = json_decode($user_cookie, true);
        }

        if( isset( $user_info['user_id'] ) ){
            return $user_info['user_id'];
        }
        return 0;
    }

    /**
     * 用户登出
     */
    public function logout(){
        $user_cookie = \Think\Crypt::decrypt( cookie('sd_admin_user'), C('MY_COOKIE_KEY') );//获取解密数据
        $user_info = array();
        if($user_cookie){
            $user_info = json_decode($user_cookie, true);
        }
        if( !empty( $user_info ) ){
            cookie('sd_admin_user', null);
            session('sd_admin_action', null);
        }

        $this->success('退出成功', '/Admin/Login', 3);

    }
}