<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller {
	//登陆界面
	public function login(){
        $this->display();
    }
	
	//下线
	public function logout(){
		session('[destroy]');
		cookie("PHPSESSID",null);
        $this->display("login");
    }
	
	
	//生成验证码
	public function verify(){
		
		$Verify = new \Think\Verify();
		$Verify->useNoise 	= true;
		$Verify->length   	= 4;
		$Verify->useImgBg 	= false;
		$Verify->fontSize 	= 18;
		$Verify->imageH 	= 35;
		$Verify->imageW 	= 0;
		$Verify->useCurve 	= false;
		$Verify->entry(1);

	}
	
	// 检测输入的验证码是否正确，$code为用户输入的验证码字符串
	private function check_verify($code, $id = 1){
		$verify = new \Think\Verify();
		return $verify->check($code, $id);
	}

	//登陆检查
	public function check(){
		$fcode=I('post.verify','');
		
		if(!$this->check_verify($fcode)){
			$this->error("验证码错误！！！",U("login"),2);
		}
			
		$uname = I('post.uname','');
		$upassword = I('post.upassword','');

		$uid = I('post.uname',0,'intval');

		$Admin = M('Admin');
		
		if($uid == 0){
			$user = $Admin->where("uname='%s' AND upassword='%s'",array($uname,$upassword))->find();
		}else{
			$user = $Admin->where("( uname='%s' AND upassword='%s' ) OR ( uid='%d' AND upassword='%s' ) ",array($uname,$upassword,$uid,$upassword))->find();
		}
		
		
		if($user==null){
			$this->error("用户名或密码错误！！！",U("login"),2);
		}
		
		session("uname",$user["uname"]);
		session("uid",$user["uid"]);
		session("remark",$user["remark"]);
		//0 未登录 1 超级管理员 2 管理员
		session("kind",$user["kind"]);
		$this->success("登陆成功",U("Index/index"),0);
		
	}
	
	
}