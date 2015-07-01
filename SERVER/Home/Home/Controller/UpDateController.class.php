<?php
namespace Home\Controller;
use Think\Controller;

class UpDateController extends Controller {
    
	
	//  2#44			报警代码2 倾斜角度44度
    //  1#3.56#0		心跳代码1 电池电压3.56 倾斜角度0
	public function update(){
		
		try {
			$post_data = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
			//\Think\Log::record($post_data);
			//判断是否是测试请求
			if($post_data->test==="1"){
				$result["data"] = "success" ;
				$this->ajaxReturn ($result,'JSON');
			}
			
			$number = $post_data->number;
			$type = $post_data->type;
			$volt = $post_data->volt;
			$angle = $post_data->angle;
			$time = $post_data->time;
			
			/*$type = I('post.type',0,'intval');
			$volt = I('post.volt',0,'float');
			$angle = I('post.angle',0,'intval');
			$time = I('post.time','','string');*/

			
			
			$Equip = M('Equipment');
			
			$list = $Equip->where("number='%s'",array($number))->getField('id,safetime');
			
			foreach($list as $key => $value){
				$id = $key;
				$safetime = $value;
			}
			
			if($id == null){
				$result["data"] = "设备不存在" ;
				$this->ajaxReturn ($result,'JSON');
			}

			$data['id'] = $id;
			$data['type'] = $type;
			$data['time'] = $time;
			$data['angle'] = $angle;
			if($type==1){
				
				$data['volt'] = $volt;
				
				$validtime = strtotime($time) + $safetime;		//设置失效时间
				$data['validtime'] = date("Y-m-d H:i:s ",$validtime);
				
			}else if($type==2){
				
			}else{
				$result["data"] = "数据错误" ;
				$this->ajaxReturn ($result,'JSON');
			}
			
			$Equip->where("id='%d'",array($id))->save($data);

			$Record = M('Record');
			
			
			$data['volt'] = $volt;
			
			$Record->data($data)->add();
			
			$result["data"] = "success" ;
			$this->ajaxReturn ($result,'JSON');
			
		} catch (Exception $e) {
			$result["data"] = $e->getMessage();
			$this->ajaxReturn($result, 'JSON');
		}
		
	}
	
}