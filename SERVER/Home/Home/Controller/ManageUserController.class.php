<?php
namespace Home\Controller;
use Think\Controller;

class ManageUserController extends SuperController {
	
	//查询
    public function retrieve(){
		
		$uid = session("uid");
		$kind = session("kind");
		
		$Admin = M('Admin');
		$list = $Admin->where("kind=2")->getField('uid,uname,remark');
		//$list = $Admin->select();
		//show_bug($list);
		foreach($list as $entitiy){
			$result["data"][count($result["data"])]=$entitiy;
		}
		$this->ajaxReturn ($result,'JSON');
		
    }
	
	//添加
	public function create(){
		
		try {
			
			$uname = I('post.uname','','string');
			$upassword = I('post.upassword','','string');
			$remark = I('post.remark','','string');

			$Admin = M('Admin');

			$old_data = $Admin->where("uname='%s'",array($uname))->select();
			if(count($old_data)> 0){
				$result["data"] = "用户名已存在，请更换！" ;
				$this->ajaxReturn ($result,'JSON');
			}
			
			$data['uname'] = $uname;
			$data['upassword'] = $upassword;
			$data['remark'] = $remark;
			$data['kind'] = 2;

			$uid = $Admin->data($data)->add();
			
			$result["data"] = "success" ;
			$result["uid"] =$uid ;
			$this->ajaxReturn ($result,'JSON');
			
		} catch (Exception $e) {
			$result["data"] = $e->getMessage();
			$this->ajaxReturn($result, 'JSON');
		}
		
	}
	
	//更新
	public function update(){
		
		try {
			
			$uid = I('post.uid',0,'intval');
			$uname = I('post.uname','','string');
			$upassword = I('post.upassword','','string');
			$remark = I('post.remark','','string');
			
			if(strlen($uname)< 4){
				$result["data"] = "用户名少于4位" ;
				$this->ajaxReturn ($result,'JSON');
			}
			
			$Admin = M('Admin');
			$old_data = $Admin->where("uid='%d'",array($uid))->select();
			if(count($old_data)==0){
				$result["data"] = "用户不存在" ;
				$this->ajaxReturn ($result,'JSON');
			}

			$old_data = $Admin->where("uid<>'%d' AND uname='%s'",array($uid,$uname))->select();
			if(count($old_data)> 0){
				$result["data"] = "用户名已存在，请更换！" ;
				$this->ajaxReturn ($result,'JSON');
			}
			
			$data['uname'] = $uname;
			if(strlen($upassword)){
				$data['upassword'] = $upassword;
			}
			
			$data['remark'] = $remark;

			$Admin->where("uid='%d'",array($uid))->save($data);

			$result["data"] = "success" ;
			$this->ajaxReturn ($result,'JSON');
			
		} catch (Exception $e) {
			$result["data"] = $e->getMessage();
			$this->ajaxReturn($result, 'JSON');
		}
		
		
	}
	
	//删除
	public function delete(){
		
		try {
			
			$uid = I('post.uid',0,'intval');

			$Admin = M('Admin');
			$old_data = $Admin->where("uid='%d'",array($uid))->delete();

			$result["data"] = "success" ;
			$this->ajaxReturn ($result,'JSON');
			
		} catch (Exception $e) {
			$result["data"] = $e->getMessage();
			$this->ajaxReturn($result, 'JSON');
		}
		
		
		
	}
	
}