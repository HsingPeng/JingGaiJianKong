<?php
namespace Home\Controller;
use Think\Controller;
class GetCurrentController extends BaseController {
    public function getcurrent(){
		
		set_time_limit(30);			//设置超时
		$time = time();
		
		ignore_user_abort(FALSE);	//用户断开不继续执行代码
		
		$uid = session("uid");
		$kind = session("kind");
		
		$config_t = json_decode($_POST['data']);
		//show_bug($config_t);
		
		session_write_close();		//关闭session锁定，防止PHP阻塞
		
		if($config_t->init){		//第一次获取所有数据
			
			$this->init($kind,$uid);
			
		}elseif($config_t->status == 0){		//代表断线重连
			$this->send();
		}else{
			
			while(connection_status()==0){
				usleep(0.5*1000000);		//微妙 万分之一秒
				
				$this->check($config_t,$kind,$uid);		//检测是否有新数据
				
				echo str_pad("",256);		//发送大点垃圾数据，保持响应
    			flush();
				
				//\Think\Log::record(connection_status());
				
				if(time()-$time > 27){		//在超时之前结束连接
					$this->send();
					break;
				}
				
				
			}

			//\Think\Log::record(connection_status());
			
		}
		
		
    }
	
	//第一次获取所有数据
	private function init($kind,$uid){
		$Equip = M('Equipment');
		
		if($kind==1){
			$data = $Equip->select();
		}else{
			$data = $Equip->where("uid='%d'",array($uid))->select();
		}

		$new_data = $this->handleSafeTime($data);

		$result["data"] = $new_data ;
		$result["info"] = "初始化" ;
		$result["updatedTime"] = time() ;
		$this->ajaxReturn ($result,'JSON');
	}
	
	//检测是否有更新
	private function check($config_t,$kind,$uid){
		$Equip = M('Equipment');
		
		$updatedTime = date("Y-m-d H:i:s ",$config_t->updatedTime);
		
		if($kind==1){
			$data = $Equip->where("time>='%s' OR ( type=1 AND validtime>'%s' AND validtime<'%s')",array($updatedTime,$updatedTime,date("Y-m-d H:i:s ",time())))->select();
		}else{
			$data = $Equip->where("uid='%d' AND ( time>='%s' OR ( type=1 AND validtime>'%s' AND validtime<'%s') )",array($uid,$updatedTime,$updatedTime,time()))->select();
		}
		
		
			if(count($data)){
				
				$new_data = $this->handleSafeTime($data);
				
				$result["data"] = $new_data ;
				$result["info"] = "有数据" ;
				$result["updatedTime"] = time() ;
				$this->ajaxReturn ($result,'JSON');
				break;
			}
			
		}
	
	//没有数据时发送结束数据
	private function send(){
		$data = array() ;
		$result["data"] = $data ;
		$result["info"] = "没有数据" ;
		$result["updatedTime"] = time();
		$this->ajaxReturn ($result,'JSON');
	}
	
	//标记超过心跳时间的数据type=0丢失通信
	private function handleSafeTime($data){
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
	
}