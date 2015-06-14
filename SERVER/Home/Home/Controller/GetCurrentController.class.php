<?php
namespace Home\Controller;
use Think\Controller;
class GetCurrentController extends Controller {
    public function getcurrent(){
		
		set_time_limit(30);			//设置超时
		$time = time();
		ignore_user_abort(FALSE);
		
		$config_t = json_decode($_POST['data']);
		//show_bug($config_t);
		
		session_write_close();		//关闭session锁定，防止PHP阻塞
		
		if($config_t->init){
			
			$Equip = M('Equipment');
			$data = $Equip->select();
			$result["data"] = $data ;
			$result["updatedTime"] = time() ;
			$this->ajaxReturn ($result,'JSON');
			
		}elseif($config_t->status == 0){
			$this->send($config_t);
		}else{
			
			while(connection_status()==0){
				usleep(0.5*1000000);		//微妙 万分之一秒
				
				$this->check($config_t);
				
				echo str_pad("",256);		//发送大点垃圾数据，保持响应
    			flush();
				
				//\Think\Log::record(connection_status());
				
				if(time()-$time > 27){		//在超时之前结束连接
					$this->send($config_t);
					break;
				}
				
				
			}

			//\Think\Log::record(connection_status());
			
		}
		
		
    }
	
	//检测是否有更新
	private function check($config_t){
			$Equip = M('Equipment');
		
			//$data = $Equip->where('time>='+$config_t->updatedTime)->select();
			$data = $Equip->where("time>='%s'",array(date("Y-m-d H:i:s ",$config_t->updatedTime)))->select();
			if(count($data)){
				$result["data"] = $data ;
				$result["updatedTime"] = time() ;
				$this->ajaxReturn ($result,'JSON');
				break;
			}
			
		}
	
	//没有数据时发送结束数据
	private function send($config_t){
		$data = array() ;
		$result["data"] = $data ;
		$result["updatedTime"] = $config_t->updatedTime ;
		$this->ajaxReturn ($result,'JSON');
	}
	
}