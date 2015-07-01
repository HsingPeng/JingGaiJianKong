<?php
namespace Home\Controller;
use Think\Controller;

class HistoryController extends BaseController {
	
	//查询
    public function retrieve(){
		
		$uid = session("uid");
		$kind = session("kind");
		
		$Record = M('Record');
		
		if($kind==1){
			$data = $Record->order('rid desc')->select();
		}else{
			$data = $Record->table('equipment eq,record re')->where("eq.uid='%d' AND eq.id=re.id",array($uid))->order('rid desc')->select();
		}
		
		$result['data']=$data;
		
		$this->ajaxReturn ($result,'JSON');
		
    }
	
	//删除
	public function delete(){
		
		try {
			
			$rid = I('post.rid',0,'intval');		//获取post参数

			$uid = session("uid");
			$kind = session("kind");
			
			$Record = M('Record');
			
			if($kind==1){
				$Record->where("rid='%d'",array($rid))->delete();
			}else{
				$data = $Record->table('equipment eq,record re')->where("eq.uid='%d' AND re.rid='%d' AND eq.id=re.id",array($uid,$rid))->select();
				if(count($data)==0){
					$result["data"] = "删除错误！" ;
					$this->ajaxReturn ($result,'JSON');
				}else{
					$Record->where("rid='%d'",array($rid))->delete();
				}
			}
			
			

			$result["data"] = "success" ;
			$this->ajaxReturn ($result,'JSON');
			
		} catch (Exception $e) {
			$result["data"] = $e->getMessage();
			$this->ajaxReturn($result, 'JSON');
		}

	}
	
	//删除所有
	public function deleteALL(){
		
		try {

			$uid = session("uid");
			$kind = session("kind");
			
			$Record = M('Record');
			
			if($kind==1){
				$Record->where('1')->delete();
			}else{
				$data = $Record->table('equipment eq,record re')->where("eq.uid='%d' AND eq.id=re.id",array($uid))->select();

				$rid = array();
				
				foreach($data as $entity){
					 array_push($rid,$entity['rid']);
				}
				
				$where = 'rid in('.implode(',',$rid).')';
				$Record->where($where)->delete();
				
			}

			$result["data"] = "success" ;
			$this->ajaxReturn ($result,'JSON');
			
		} catch (Exception $e) {
			$result["data"] = $e->getMessage();
			$this->ajaxReturn($result, 'JSON');
		}

	}
	
}