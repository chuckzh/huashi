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
        $php_url = 'http://' . $_SERVER['HTTP_HOST'] . '/';

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
        $config['savePath'] = 'image/';
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
            $ret = array('error' => 0, 'url' => '/' . $config['rootPath'] . $info['imgFile']['savepath'] . $info['imgFile']['savename']);
            make_json_response( $ret );
        }
    }

    public function Teacher_add(){
        $t_id = 0;
        $t_name = '';
        $t_photo = '';
        $t_job = '';
        $t_email = '';
        $t_introduce = '';

        $t_id = I('get.t_id', 0, 'intval');
        if( $t_id ){
            $ret = M('teacher')->where( 't_id = ' . $t_id )->find();
            $ret && ( extract( $ret ) );
        }

        $this->assign('t_id', $t_id);
        $this->assign('t_name', $t_name);
        $this->assign('t_photo', $t_photo);
        $this->assign('t_job', $t_job);
        $this->assign('t_email', $t_email);
        $this->assign('t_introduce', $t_introduce);

        $this->display();
    }

    public function teacherAdd(){
        $t_name = I('put.t_name', '', 'trim');
        $t_job = I('put.t_job', '', 'trim');
        $t_photo = I('put.t_photo', '', 'trim');
        $t_introduce = I('put.t_introduce', '', '');
        $t_email = I('put.t_email', '', '/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/');

        $t_id = I('put.t_id', 0, 'intval');

        if( empty( $t_name ) || empty( $t_photo ) ){
            make_general_response('', '-1', '照片和姓名不能为空' );
        }

        $mod = M('teacher');
        $mod->t_name = $t_name;
        $mod->t_job = $t_job;
        $mod->t_photo = $t_photo;
        $mod->t_introduce = $t_introduce;
        $mod->t_email = $t_email;

        if( !$t_id ){
            $ret = $mod->add();
        }else{
            $mod->t_id = $t_id;
            $ret = $mod->save();
        }

        if( $ret ){
            make_general_response('', '0', '操作成功');
        }else {
            make_general_response('', '-1', '操作失败');
        }
    }

    public function teacher_List(){
        $mod = M('teacher');

        $count = $mod->count();
        $Page  = new \Think\Page($count, 15);
        $show = $Page->show();

        $rows = $mod->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('rows', $rows);
        $this->assign('page', $show);

        $this->display();

    }

    public function Teacher_depaadd(){

        $d_id = 0;
        $d_name = '';

        $d_id = I('get.d_id', 0, 'intval');
        if( $d_id ){
            $ret = M('teacherDep')->where( 'd_id = ' . $d_id )->find();
            $ret && ( extract( $ret ) );
        }

        $this->assign('d_id', $d_id);
        $this->assign('d_name', $d_name);

        $this->display();
    }
    public function teacherDepaAdd(){
        $d_name = I('put.d_name', '', 'trim');
        $d_id = I('put.d_id', 0, 'intval');

        $mod = M('teacherDep');
        $mod->d_name = $d_name;

        if( !$d_id ){
            $ret = $mod->add();
        }else{
            $mod->d_id = $d_id;
            $ret = $mod->save();
        }

        if( $ret ){
            make_general_response('', '0', '操作成功');
        }else{
            make_general_response('', '-1', '操作失败');
        }
    }

    public function Teacher_depalist(){
        $mod = M('teacherDep');

        $count = $mod->count();
        $Page  = new \Think\Page($count, 15);
        $show = $Page->show();

        $rows = $mod->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('rows', $rows);
        $this->assign('page', $show);

        $this->display();

    }

    public function Student_add(){
        $s_id = 0;
        $s_name = '';
        $s_no = '';
        $s_sex = '';
        $s_phone = '';
        $s_introduce = '';

        $s_id = I('get.s_id', 0, 'intval');
        if( $s_id ){
            $ret = M('student')->where( 's_id = ' . $s_id )->find();
            $ret && ( extract( $ret ) );
        }

        $this->assign('s_id', $s_id);
        $this->assign('s_name', $s_name);
        $this->assign('s_no', $s_no);
        $this->assign('s_sex', $s_sex);
        $this->assign('s_phone', $s_phone);
        $this->assign('s_introduce', $s_introduce);

        $this->display();
    }

    public function studentAdd(){
        $s_name = I('put.s_name', '', 'trim');
        $s_no = I('put.s_no', '', 'trim');
        $s_sex = I('put.s_sex', 0, 'intval');
        $s_introduce = I('put.s_introduce', '', 'trim');
        $s_phone = I('put.s_phone', '', 'trim');

        $s_id = I('put.s_id', 0, 'intval');

        if( empty( $s_name ) ){
            make_general_response('', '-1', '姓名不能为空' );
        }

        $mod = M('student');
        $mod->s_name = $s_name;
        $mod->s_no = $s_no;
        $mod->s_sex = $s_sex;
        $mod->s_introduce = $s_introduce;
        $mod->s_phone = $s_phone;

        if( !$s_id ){
            $ret = $mod->add();
        }else{
            $mod->s_id = $s_id;
            $ret = $mod->save();
        }

        if( $ret ){
            make_general_response('', '0', '操作成功');
        }else {
            make_general_response('', '-1', '操作失败');
        }
    }

    public function student_list(){
        $mod = M('student');

        $count = $mod->count();
        $Page  = new \Think\Page($count, 15);
        $show = $Page->show();

        $rows = $mod->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('rows', $rows);
        $this->assign('page', $show);

        $this->display();
    }

    public function Class_add(){

        $class_id = 0;
        $class_name = '';

        $class_id = I('get.class_id', 0, 'intval');
        if( $class_id ){
            $ret = M('class')->where( 'class_id = ' . $class_id )->find();
            $ret && ( extract( $ret ) );
        }

        $this->assign('class_id', $class_id);
        $this->assign('class_name', $class_name);

        $this->display();
    }

    public function classAdd(){
        $class_name = I('put.class_name', '', 'trim');

        $class_id = I('put.class_id', 0, 'intval');

        if( empty( $class_name ) ){
            make_general_response('', '-1', '名称不能为空' );
        }

        $mod = M('class');
        $mod->class_name = $class_name;

        if( !$class_id ){
            $ret = $mod->add();
        }else{
            $mod->class_id = $class_id;
            $ret = $mod->save();
        }

        if( $ret ){
            make_general_response('', '0', '操作成功');
        }else {
            make_general_response('', '-1', '操作失败');
        }
    }

    public function Class_list(){
        $mod = M('class');

        $count = $mod->count();
        $Page  = new \Think\Page($count, 15);
        $show = $Page->show();

        $rows = $mod->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('rows', $rows);
        $this->assign('page', $show);

        $this->display();
    }

    public function Content_classadd(){

        $c_id = 0;
        $c_name = '';
        $c_parent_id = 0;
        $c_order = '';

        $c_id = I('get.c_id', 0, 'intval');
        if( $c_id ){
            $ret = M('contentClass')->where( 'c_id = ' . $c_id )->find();
            $ret && ( extract( $ret ) );
        }

        $class_arr = $this->contentClassArr( 0, 1 , true);
        $class_array = array();
        foreach ($class_arr as $c1) {//1级分类
            $c1_childs = $this->contentClassArr( $c1['c_id'], 2, true);//2级
            $class_array[] = $c1;
            !empty( $c1_childs ) && ( $class_array = array_merge( $class_array, $c1_childs ) );
            foreach ($c1_childs as $c2) {
                // $c2['childs'] = $this->contentClassArr( $c2['c_id'], 3, true );//3级
                $c2_childs = $this->contentClassArr( $c2['c_id'], 3, true );//3级
                !empty( $c2_childs ) && ( $class_array = array_merge( $class_array, $c2_childs ) );
            }
        }

        $this->assign('c_id', $c_id);
        $this->assign('c_name', $c_name);
        $this->assign('c_parent_id', $c_parent_id);
        $this->assign('c_order', $c_order);
        $this->assign('class_arr', $class_array);

        $this->display();
    }

    public function contentClassAdd(){
        $c_name = I('put.c_name', '', 'trim');
        $c_parent_id = I('put.c_parent_id', 0, 'intval');
        $c_order = I('put.c_order', 0, 'intval');

        $c_id = I('put.c_id', 0, 'intval');

        if( empty( $c_name ) ){
            make_general_response('', '-1', '名称不能为空' );
        }

        $mod = M('contentClass');
        $mod->c_name = $c_name;
        $mod->c_parent_id = $c_parent_id;
        $mod->c_order = $c_order;

        if( !$c_id ){
            $ret = $mod->add();
        }else{
            $mod->c_id = $c_id;
            $ret = $mod->save();
        }

        if( $ret ){
            make_general_response('', '0', '操作成功');
        }else {
            make_general_response('', '-1', '操作失败');
        }
    }

    public function Content_classlist(){
        $mod = M('contentClass');

        $count = $mod->count();
        $Page  = new \Think\Page($count, 15);
        $show = $Page->show();

        $rows = $mod->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('rows', $rows);
        $this->assign('page', $show);

        $this->display();
    }

    /**
     * 获取直接父类的子类
     * @param  integer $parent_id
     * @param  integer $level
     * @param  boolean $nbsp
     * @return [type]
     */
    private function contentClassArr( $parent_id = 0, $level = 1 , $nbsp = false){
        $arr = array();
        $mod = M('contentClass');
        static $classArr;
        !$classArr && ( $classArr = $mod->select() );
        foreach ($classArr as $class) {
            if( $class['c_parent_id'] == $parent_id ){
                $class['level'] = $level;
                $nbsp = '';
                for( $n = 0; $n < pow(4, $level);$n++)
                    $nbsp .= '&nbsp;';
                $nbsp && ( $class['c_name'] = $nbsp . $class['c_name']);
                $arr[] = $class;
            }
        }

        return $arr;

    }

    public function Content_add(){
        $c_id = 0;
        $c_title = '';
        $c_class_id = 0;
        $c_content = '';

        $c_id = I('get.c_id', 0, 'intval');
        if( $c_id ){
            $ret = M('contents')->where( 'c_id = ' . $c_id )->find();
            $ret && ( extract( $ret ) );
        }

        $this->assign('c_id', $c_id);
        $this->assign('c_title', $c_title);
        $this->assign('c_class_id', $c_class_id);
        $this->assign('c_content', $c_content);

        $this->display();
    }

    public function contentAdd(){
        $c_title = I('put.c_title', '', 'trim');
        $c_class_id = I('put.c_class_id', '', 'trim');
        $c_content = I('put.c_content', '', 'trim');

        $c_id = I('put.c_id', 0, 'intval');

        if( empty( $c_title ) || empty( $c_content ) ){
            make_general_response('', '-1', '标题和内容不能为空' );
        }

        $mod = M('contents');
        $mod->c_title = $c_title;
        $mod->c_class_id = $c_class_id;
        $mod->c_content = $c_content;
        $mod->c_time = time();

        if( !$c_id ){
            $ret = $mod->add();
        }else{
            $mod->c_id = $c_id;
            $ret = $mod->save();
        }

        if( $ret ){
            make_general_response('', '0', '操作成功');
        }else {
            make_general_response('', '-1', '操作失败');
        }
    }

    public function sysConfig(){
        $mod = M('webConfig');
        $map = array('code' => array( 'in', array('web_name', 'web_ICP') ) );

        $cfg = $mod->where( $map )->getField('code, id, value');
        $web_name = $web_ICP = null;
        extract( $cfg );

        $this->assign('web_name', $web_name);
        $this->assign('web_ICP', $web_ICP);

        $this->display();
    }

    public function sysConfigAdd(){
        $web_name = I('put.web_name', '', 'trim');
        $web_ICP = I('put.web_ICP', '', 'trim');

        $web_name_id = I('put.web_name_id', 0, 'intval');
        $web_ICP_id = I('put.web_ICP_id', 0, 'intval');

        $mod = M('webConfig');

        //*****************web_name******************
        $mod->code = 'web_name';
        $mod->value = $web_name;

        if( $web_name_id ){
            $mod->id = $web_name_id;
            $ret = $mod->save();
        }else{
            $ret = $mod->add();
        }
        $ret === false && make_general_response('', '-1', '操作失败');

        //*****************web_ICP******************
        $mod->code = 'web_ICP';
        $mod->value = $web_ICP;

        if( $web_ICP_id ){
            $mod->id = $web_ICP_id;
            $ret = $mod->save();
        }else{
            $mod->id = null;
            $ret = $mod->add();
        }

        $ret === false && make_general_response('', '-1', '操作失败');

        make_general_response('', '0', '操作成功');
    }

    public function test(){
        echo json_encode( \Think\Crypt::encrypt('123456', C('MY_DATA_KEY')) );
    }




}