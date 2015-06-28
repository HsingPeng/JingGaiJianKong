<?php
namespace Home\Controller;
use Think\Controller;

class SettingController extends BaseController {
	
	/**
	*修改当前用户密码
	*/
	public function updateAdmin() {
		
		try {
		
			$uid = session("uid");

			$uname = I('post.uname','','string');
			$upassword = I('post.upassword','','string');
			$old_upassword = I('post.old_upassword','','string');
			$remark = I('post.remark','','string');

			if(strlen($uname)< 4){
				$result["data"] = "用户名少于4位" ;
				$this->ajaxReturn ($result,'JSON');
			}
			
			$data['uname'] = $uname;
			
			$Admin = M('Admin');
			
			if(strlen($upassword)){
				
				$user = $Admin->where("uid='%d' AND upassword='%s'",array($uid,$old_upassword))->find();
				if(count($user)==0){
					$result["data"] = "旧密码输入错误" ;
					$this->ajaxReturn ($result,'JSON');
				}
				$data['upassword'] = $upassword;
			}
			$data['remark'] = $remark;

			
			$Admin->where("uid='%d'",array($uid))->save($data);	
		
			session("uname",$data["uname"]);
			session("remark",$data["remark"]);
			
			$result["data"] = "success" ;
			$this->ajaxReturn ($result,'JSON');
			
		}catch (Exception $e) {
			$result["data"] = $e->getMessage();
			$this->ajaxReturn($result, 'JSON');
		}
	}
}