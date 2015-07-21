<?php
namespace Home\Controller;
use Think\Controller;

class ManageJGController extends BaseController {
	
	//查询
    public function retrieve(){
		
		$uid = session("uid");
		$kind = session("kind");
		
		$Equip = M('Equipment');
		if($kind==1){
			$data = $Equip->select();
		}else{
			$data = $Equip->where("uid='%d'",array($uid))->select();
		}
		
		$result["data"] = $data ;
		$this->ajaxReturn ($result,'JSON');
		
    }
	
	//添加
	public function create(){
		
		try {
			
			$safetime = I('post.safetime',0,'intval');
			$number = I('post.number','','string');
			$lng = I('post.lng',0,'float');
			$lat = I('post.lat',0,'float');
			$address = I('post.address','','string');
			$describe = I('post.describe','','string');
			$maxangle = I('post.maxangle',15,'intval');
			$minangle = I('post.minangle',0,'intval');
			$uid = I('post.uid',0,'intval');
			
			if($safetime < 3600){
				$result["data"] = "心跳时间过短" ;
				$this->ajaxReturn ($result,'JSON');
			}

			$Admin = M('Admin');
			
			$exist_admin = $Admin->where("uid='%d'",array($uid))->select();
			if(count($exist_admin) == 0){
				$result["data"] = "该管理员不存在" ;
				$this->ajaxReturn ($result,'JSON');
			}
			
			$Equip = M('Equipment');

			$data['safetime'] = $safetime;
			$data['number'] = $number;
			$data['lng'] = $lng;
			$data['lat'] = $lat;
			$data['address'] = $address;
			$data['describe'] = $describe;
			$data['maxangle'] = $maxangle;
			$data['minangle'] = $minangle;
			if(session("kind")!=1){
				$uid = session("uid");
			}
			$data['uid'] = $uid;

			$id = $Equip->data($data)->add();
			
			$result["data"] = "success" ;
			$result["id"] =$id ;
			$this->ajaxReturn ($result,'JSON');
			
		} catch (Exception $e) {
			$result["data"] = $e->getMessage();
			$this->ajaxReturn($result, 'JSON');
		}
		
	}
	
	//更新
	public function update(){
		
		try {
			
			$id = I('post.id',0,'intval');
			$number = I('post.number','','string');
			$lng = I('post.lng',0,'float');
			$lat = I('post.lat',0,'float');
			$address = I('post.address','','string');
			$describe = I('post.describe','','string');
			$safetime = I('post.safetime',259200,'intval');
			$maxangle = I('post.maxangle',15,'intval');
			$minangle = I('post.minangle',0,'intval');
			
			if($maxangle < $minangle){
				$result["data"] = "角度上限小于角度下限！" ;
				$this->ajaxReturn ($result,'JSON');
			}
			
			if(session("kind")==1){
				$uid = I('post.uid',0,'intval');
				
				$Admin = M('Admin');
			
				$exist_admin = $Admin->where("uid='%d'",array($uid))->select();
				if(count($exist_admin) == 0){
					$result["data"] = "该管理员不存在" ;
					$this->ajaxReturn ($result,'JSON');
				}
				
			}
			
			$Equip = M('Equipment');
			$old_data = $Equip->where("id='%d'",array($id))->select();
			if(count($old_data)==0){
				$result["data"] = "设备不存在" ;
				$this->ajaxReturn ($result,'JSON');
			}

			$data['id'] = $id;
			$data['number'] = $number;
			$data['lng'] = $lng;
			$data['lat'] = $lat;
			$data['address'] = $address;
			$data['safetime'] = $safetime;
			$data['describe'] = $describe;
			$data['maxangle'] = $maxangle;
			$data['minangle'] = $minangle;
			if(session("kind")!=1){
				$uid = session("uid");
			}
			$data['uid'] = $uid;
			

			$Equip->where("id='%d'",array($id))->save($data);

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
			
			$id = I('post.id',0,'intval');

			$Equip = M('Equipment');
			$Equip->where("id='%d'",array($id))->delete();

			$result["data"] = "success" ;
			$this->ajaxReturn ($result,'JSON');
			
		} catch (Exception $e) {
			$result["data"] = $e->getMessage();
			$this->ajaxReturn($result, 'JSON');
		}
		
		
		
	}
	
}