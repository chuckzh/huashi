<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonAdminController {
    public function index(){

    	$this->display();

    }

    public function start(){
    	$this->display();

    }

    public function left(){
    	$this->display();

    }

    public function right(){
    	$this->display();

    }

    public function top(){
    	$this->display();

    }

    public function footer(){
    	$this->display();

    }

    public function fileBrowse(){
        $php_path = str_replace( '\\', '/', $_SERVER['DOCUMENT_ROOT'] ) . '/';
        $php_url = $_SERVER['HTTP_HOST'] . '/';

        //根目录路径，可以指定绝对路径，比如 /var/www/attached/
        $root_path = $php_path . 'data/';
        //根目录URL，可以指定绝对路径，比如 http://www.yoursite.com/attached/
        $root_url = $php_url . 'data/';
        //图片扩展名
        $ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp');

        //目录名
        $dir_name = empty($_GET['dir']) ? '' : trim($_GET['dir']);
        if (!in_array($dir_name, array('', 'image', 'flash', 'media', 'file'))) {
            echo "Invalid Directory name.";
            exit;
        }
        if ($dir_name !== '') {
            $root_path .= $dir_name . "/";
            $root_url .= $dir_name . "/";
            if (!file_exists($root_path)) {
                mkdir($root_path);
            }
        }

        //根据path参数，设置各路径和URL
        if (empty($_GET['path'])) {
            $current_path = realpath($root_path) . '/';
            $current_url = $root_url;
            $current_dir_path = '';
            $moveup_dir_path = '';
        } else {
            $current_path = realpath($root_path) . '/' . $_GET['path'];
            $current_url = $root_url . $_GET['path'];
            $current_dir_path = $_GET['path'];
            $moveup_dir_path = preg_replace('/(.*?)[^\/]+\/$/', '$1', $current_dir_path);
        }

        //不允许使用..移动到上一级目录
        if (preg_match('/\.\./', $current_path)) {
            echo 'Access is not allowed.';
            exit;
        }

        //最后一个字符不是/
        if (!preg_match('/\/$/', $current_path)) {
            echo 'Parameter is not valid.';
            exit;
        }

        //目录不存在或不是目录
        if (!file_exists($current_path) || !is_dir($current_path)) {
            echo 'Directory does not exist.';
            exit;
        }

        //遍历目录取得文件信息
        $file_list = array();
        if ($handle = opendir($current_path)) {
            $i = 0;
            while (false !== ($filename = readdir($handle))) {
                if ($filename{0} == '.') continue;
                $file = $current_path . $filename;
                if (is_dir($file)) {
                    $file_list[$i]['is_dir'] = true; //是否文件夹
                    $file_list[$i]['has_file'] = (count(scandir($file)) > 2); //文件夹是否包含文件
                    $file_list[$i]['filesize'] = 0; //文件大小
                    $file_list[$i]['is_photo'] = false; //是否图片
                    $file_list[$i]['filetype'] = ''; //文件类别，用扩展名判断
                } else {
                    $file_list[$i]['is_dir'] = false;
                    $file_list[$i]['has_file'] = false;
                    $file_list[$i]['filesize'] = filesize($file);
                    $file_list[$i]['dir_path'] = '';
                    $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $file_list[$i]['is_photo'] = in_array($file_ext, $ext_arr);
                    $file_list[$i]['filetype'] = $file_ext;
                }
                $file_list[$i]['filename'] = $filename; //文件名，包含扩展名
                $file_list[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
                $i++;
            }
            closedir($handle);
        }

        usort($file_list, 'file_cmp_func');

        $result = array();
        //相对于根目录的上一级目录
        $result['moveup_dir_path'] = $moveup_dir_path;
        //相对于根目录的当前目录
        $result['current_dir_path'] = $current_dir_path;
        //当前目录的URL
        $result['current_url'] = $current_url;
        //文件数
        $result['total_count'] = count($file_list);
        //文件列表数组
        $result['file_list'] = $file_list;

        //输出JSON字符串
        make_json_response( $result );
    }

    public function saveFile(){
        $config['rootPath'] = C('UPLOAD_ROOT');
        $config['savePath'] = C('image');
        $config['subName'] = date('Ymd');
        $config['saveName'] = strval( time() ) . rand_str();
        $config['exts'] = array('jpg', 'gif', 'png', 'jpeg');
        $config['maxSize'] = 3145728;
        $upload = new \Think\Upload( $config );
        $info = $upload->upload();

        if( !$info ){
            $ret = array('error' => 1, 'msg' => $upload->getError() );
            make_json_response( $ret );
        }else{
            $ret = array('error' => 0, 'url' => $info['savepath'] . $info['savename']);
            make_json_response( $ret );
        }
    }

    public function test(){
        echo json_encode( \Think\Crypt::encrypt('123456', C('MY_DATA_KEY')) );
    }




}