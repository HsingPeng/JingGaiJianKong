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
			$uid = I('post.uid',0,'intval');
			
			if($safetime < 3600){
				$result["data"] = "心跳时间过短" ;
				$this->ajaxReturn ($result,'JSON');
			}

			$Equip = M('Equipment');
			
			/*$old_data = $Equip->where("id='%d'",array($id))->select();
			if(count($old_data)){
				$result["data"] = "设备ID已存在" ;
				$this->ajaxReturn ($result,'JSON');
			}*/

			$data['safetime'] = $safetime;
			$data['number'] = $number;
			$data['lng'] = $lng;
			$data['lat'] = $lat;
			$data['address'] = $address;
			$data['describe'] = $describe;
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
			$uid = I('post.uid',0,'intval');
			
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
			$data['describe'] = $describe;
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
			$old_data = $Equip->where("id='%d'",array($id))->delete();

			$result["data"] = "success" ;
			$this->ajaxReturn ($result,'JSON');
			
		} catch (Exception $e) {
			$result["data"] = $e->getMessage();
			$this->ajaxReturn($result, 'JSON');
		}
		
		
		
	}
	
}