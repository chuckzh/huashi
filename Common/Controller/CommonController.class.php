<?php
namespace Common\Controller;
use Think\Controller;

class CommonController extends Controller {

	protected $_verifyCodeCfg = array();

	public function __construct() {
		parent::__construct();
		$this->_verifyCodeCfg = C('VERIFY_CODE_CFG');
	}

}