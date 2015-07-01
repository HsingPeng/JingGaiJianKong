<?php
namespace Home\Controller;
use Think\Controller;

class TestController extends LocalController {
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
	
	//home.php?m=Home&c=Test&a=test2
	public function test2(){
		$Equip = M('Equipment');
		$old_data = $Equip->where("id>'%d'",array(9))->delete();
		echo "ok" ;
	}
	
	//home.php?m=Home&c=Test&a=test3
	public function test3() {
		$Equip = M('Equipment');
		$data = $Equip->select();
		$data = $this->handleSafeTime($data);
		show_bug($data);
	}
	
	//标记超过心跳时间的数据
	private function handleSafeTime($data){
		$length = count($data);
		for($i=0 ; $i<$length ; $i++){
				$last_time = strtotime($data[$i]['time']);
				$gap_time = time() - $last_time ;
				if($gap_time>$data[$i]['safetime']){
					$data[$i]['type']=0;
				}
			}
		return $data ;
	}
	
	//标记超过心跳时间的数据type=0丢失通信
	private function handleSafeTime1($data){
		$length = count($data);
		for($i=0 ; $i<$length ; $i++){
			
			if($data[$i]['type']==1){
				$validtime = strtotime($data[$i]['validtime']);
				$gap_time = time() - $validtime ;
				if($gap_time > 0){
					$data[$i]['type']=0;
				}
			}
		}
		return $data ;
	}
	
	public function test4() {
		$Equip = M('Equipment');
		$validtime = strtotime("2015-06-27 23:40:20") + 259200;
		$data['validtime'] = date("Y-m-d H:i:s ",$validtime);
		$Equip->where("id='%d'",array(10))->save($data);
		show_bug($validtime);
	}
	
	//home.php?m=Home&c=Test&a=test5
	public function test5(){
		$Equip = M('Equipment');
		$data = $Equip->where("time>='%s' OR ( type=1 AND validtime>'%s' AND validtime<'%s')",array("2015-06-27 23:40:20","2015-06-27 23:40:20",date("Y-m-d H:i:s ",time())))->select();
		show_bug($data);
	}
	
	public function test6() {
		$Equip = M('Equipment');
		$data = $Equip->select();
		$data = $this->handleSafeTime1($data);
		show_bug($data);
	}
	
	//home.php?m=Home&c=Test&a=test7
	public function test7() {
		$Equip = M('Equipment');
		$list = $Equip->where("number='%s'",array('15555215556'))->getField('id,safetime');
		foreach($list as $key => $value){
			show_bug($key);
		}
		
	}

	
}