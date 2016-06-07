<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
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

    public function test($value='')
    {
        echo json_encode( \Think\Crypt::encrypt('123456', C('MY_DATA_KEY')) );
    }


}