<?php
namespace Home\Controller;
use Think\Controller;

//本地控制器，只允许本机访问
class LocalController extends Controller {
	
	/**
     * 架构函数
     * @access public
     */
	public function __construct() {
		
		parent::__construct();		//必须先调用父类构造方法
		
		$name = $_SERVER["SERVER_NAME"];
		if($name=='localhost'||$name=='127.0.0.1'){
			
		}else{
			$this->error("当前登录无效，请重新登陆！！！","home.php?m=Home&c=Login&a=logout",1);
		}
		
	}
}