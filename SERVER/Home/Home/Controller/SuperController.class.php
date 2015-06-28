<?php
namespace Home\Controller;
use Think\Controller;

//超级管理员控制器，只允许超级管理员进入
class SuperController extends Controller {
	
	/**
     * 架构函数
     * @access public
     */
	public function __construct() {
		
		parent::__construct();		//必须先调用父类构造方法
		
		$kind = session("kind");
		if($kind!=1){
			$this->error("权限不足！！！",U("Login/logout"),1);
		}
		
	}
}