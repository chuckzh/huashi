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

    public function test($value='')
    {
        echo crypt('7777', C('MY_DATA_KEY'));
    }


}