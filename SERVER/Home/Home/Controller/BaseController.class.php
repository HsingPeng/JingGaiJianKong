<?php
namespace Home\Controller;
use Think\Controller;

//基础控制器，过滤未登录用户
class BaseController extends Controller {
	
	/**
     * 架构函数
     * @access public
     */
	public function __construct() {
		
		parent::__construct();		//必须先调用父类构造方法
		
		$kind = session("kind");
		if($kind==null||$kind==0){
			$this->error("当前登录无效，请重新登陆！！！",U("Login/logout"),1);
		}
		
	}
}