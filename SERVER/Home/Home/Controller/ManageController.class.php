<?php
namespace Home\Controller;
use Think\Controller;

class ManageController extends Controller {
	
	//查询
    public function retrieve(){
		
		$Equip = M('Equipment');
		$data = $Equip->select();
		$result["data"] = $data ;
		$this->ajaxReturn ($result,'JSON');
		
    }
	
	//添加
	public function create(){
		
		try {
			
			$id = I('post.id',0,'intval');
			$number = I('post.number','','string');
			$lng = I('post.lng',0,'float');
			$lat = I('post.lat',0,'float');
			$address = I('post.address','','string');
			$describe = I('post.describe','','string');

			if($id < 1){
				$result["data"] = "设备ID错误" ;
				$this->ajaxReturn ($result,'JSON');
			}

			$Equip = M('Equipment');
			$old_data = $Equip->where("id='%d'",array($id))->select();
			if(count($old_data)){
				$result["data"] = "设备ID已存在" ;
				$this->ajaxReturn ($result,'JSON');
			}

			$data['id'] = $id;
			$data['number'] = $number;
			$data['lng'] = $lng;
			$data['lat'] = $lat;
			$data['address'] = $address;
			$data['describe'] = $describe;


			$Equip->data($data)->add();

			$result["data"] = "success" ;
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