<?php
namespace Home\Controller;
use Think\Controller;

class UpDateController extends Controller {
    public function test1(){
		
		$Equip = M('Equipment');
		
		$i = 501 ;
		$lng_t = 116.417854 ;
		$lat_t = 39.921988 ;
		
		for(;$i<1000;$i++){
			
			$lng_t-=0.002;
			$lat_t-=0.002;
			
				$data['id'] = $i;
				$data['number'] = 152432423;
				$data['lng'] = $lng_t;
				$data['lat'] = $lat_t;
				$data['address'] = '北京市';
				$data['describe'] = '';


				$Equip->data($data)->add();
		}
	
		echo "ok" ;
		
    }
	//home.php?m=Home&c=UpDate&a=test2
	public function test2(){
		$Equip = M('Equipment');
		$old_data = $Equip->where("id>'%d'",array(9))->delete();
		echo "ok" ;
	}
	
	//  2#44			报警代码2 倾斜角度44度
    //  1#3.56#0		心跳代码1 电池电压3.56 倾斜角度0
	public function update(){
		
		try {
			$post_data = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
			\Think\Log::record($post_data);
			
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
			
			$id = $Equip->where("number='%s'",array($number))->getField('id');
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