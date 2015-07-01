<?php
namespace Home\Controller;
use Think\Controller;

class UpDateController extends Controller {
    
	
	//  2#44			报警代码2 倾斜角度44度
    //  1#3.56#0		心跳代码1 电池电压3.56 倾斜角度0
	public function update(){
		
		try {
			$post_data = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);		//获得数据，这里是因为APP传入的POST数据是JSON格式，不是传统的键值对，需要转换。
			\Think\Log::record($GLOBALS['HTTP_RAW_POST_DATA']);
			//判断是否是测试请求
			if($post_data->test==="1"){
				$result["data"] = "success" ;
				$this->ajaxReturn ($result,'JSON');
			}
			
			//取出数据
			$number = $post_data->number;
			$type = $post_data->type;
			$volt = $post_data->volt;
			$angle = $post_data->angle;
			$time = $post_data->time;
			
			/*$type = I('post.type',0,'intval');
			$volt = I('post.volt',0,'float');
			$angle = I('post.angle',0,'intval');
			$time = I('post.time','','string');*/

			
			//实例化数据库模型
			$Equip = M('Equipment');
			
			$list = $Equip->where("number='%s'",array($number))->getField('id,safetime');		//取出号码对应的井盖ID
			
			//这里遍历是因为不知道数据的键，无法直接取出，实际里面只有一行数据
			foreach($list as $key => $value){
				$id = $key;
				$safetime = $value;
			}
			
			//如果没有取得相应的井盖
			if($id == null){
				$result["data"] = "设备不存在" ;
				$this->ajaxReturn ($result,'JSON');
			}

			$data['id'] = $id;
			$data['type'] = $type;
			$data['time'] = $time;
			$data['angle'] = $angle;
			if($type==1){				//如果是1状态正常
				
				$data['volt'] = $volt;		//取出电压
				
				$validtime = strtotime($time) + $safetime;		//设置失效时间
				$data['validtime'] = date("Y-m-d H:i:s ",$validtime);
				
			}else if($type==2){		//警告状态不需要处理
				
			}else{			//都不是说明数据有问题
				$result["data"] = "数据错误" ;
				$this->ajaxReturn ($result,'JSON');
			}
			
			$Equip->where("id='%d'",array($id))->save($data);		//写入井盖数据库

			$Record = M('Record');		//实例化记录表
			
			
			$data['volt'] = $volt;
			
			$Record->data($data)->add();		//写入记录表
			
			$result["data"] = "success" ;
			$this->ajaxReturn ($result,'JSON');		//返回结束连接
			
		} catch (Exception $e) {			//如果其中发生错误则会catch住，返回错误信息
			$result["data"] = $e->getMessage();
			$this->ajaxReturn($result, 'JSON');
		}
		
	}
	
}