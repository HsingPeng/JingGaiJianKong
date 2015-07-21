<?php
namespace Home\Controller;
use Think\Controller;
class GetCurrentController extends BaseController {
    public function getcurrent(){
		
		set_time_limit(30);			//设置超时时间
		$time = time();
		
		ignore_user_abort(FALSE);	//设置用户断开不继续执行下面的代码
		
		$uid = session("uid");			//从session里获得用户ID
		$kind = session("kind");		//从session里获得用户类别，1为超级管理员，2为普通管理员
		
		$config_t = json_decode($_POST['data']);		//从post请求中读取data变量的数据，并且从json格式转换成数组
		//show_bug($config_t);				//可以在面上显示变量的内容，方便DE_BUG
		
		session_write_close();		//关闭session锁定，防止PHP阻塞，一个客户端同时只能有一个网页访问session
		
		if($config_t->init){		//第一次获取所有数据
			
			$this->init($kind,$uid);		//转到下面的init方法
			
		}elseif($config_t->status == 0){		//代表断线重连
			$this->send();					//转到send方法，为了能让客户端立刻知道服务器已经可以连接
		}else{
			
			while(connection_status()==0){		//判断客户端是否还在连接
				usleep(0.5*1000000);		//微妙 万分之一秒 睡眠0.5秒
				
				$this->check($config_t,$kind,$uid);		//进入check方法检测是否有新数据，有则发送数据连接结束
				
				echo str_pad("",256);		//发送大点垃圾数据，保持与客户端的实时响应
    			flush();				//发送缓冲区
				
				//\Think\Log::record(connection_status());		//输出到日志文件位于home/runtime/logs/home
				
				if(time()-$time > 27){		//在超时之前结束连接，否则会报超时报错
					$this->send();		//转到send方法
					break;
				}
				
				
			}

			//\Think\Log::record(connection_status());
			
		}
		
		
    }
	
	//第一次获取所有数据
	private function init($kind,$uid){
		$Equip = M('Equipment');		//实例化equipment表模型，可以对该表进行操作
		
		if($kind==1){					//如果是超级管理员，则获得所有数据
			$data = $Equip->select();	//读取数据库
		}else{
			$data = $Equip->where("uid='%d'",array($uid))->select();		//读取当前管理员的所属的井盖数据
		}

		$new_data = $this->handleSafeTime($data);		//转入handleSafeTime方法，判断是否有丢失通信的井盖

		$result["data"] = $new_data ;					//数据存入变量
		$result["info"] = "初始化" ;						//无用数据，de_bug使用
		$result["updatedTime"] = time() ;				//传入当前时间为客户端最后一次获取数据的时间
		$this->ajaxReturn ($result,'JSON');				//使用JSON格式返回给客户端，连接结束
	}
	
	//检测是否有更新
	private function check($config_t,$kind,$uid){
		$Equip = M('Equipment');
		
		$updatedTime = date("Y-m-d H:i:s ",$config_t->updatedTime);		//取出上次获取数据的时间
		
		//判断是否是超级管理员，以获取不同数据
		if($kind==1){
			//这句语句获得通信时间在上次获取数据时间之后的所有数据，或者状态是1正常时超时时间在这期间的数据
			$data = $Equip->where("time>='%s' OR ( type=1 AND validtime>'%s' AND validtime<'%s')",array($updatedTime,$updatedTime,date("Y-m-d H:i:s ",time())))->select();
		}else{
			$data = $Equip->where("uid='%d' AND ( time>='%s' OR ( type=1 AND validtime>'%s' AND validtime<'%s') )",array($uid,$updatedTime,$updatedTime,time()))->select();
		}
		
		
			if(count($data)){		//判断是否有数据
				
				$new_data = $this->handleSafeTime($data);		//标记超过心跳时间的数据type=0表示丢失通信
				
				$result["data"] = $new_data ;
				$result["info"] = "有数据" ;
				$result["updatedTime"] = time() ;
				$this->ajaxReturn ($result,'JSON');				//使用JSON格式返回给客户端，连接结束
				break;
			}
			
		}
	
	//没有数据时发送结束数据，实际上就是内容为空的返回值
	private function send(){
		$data = array() ;
		$result["data"] = $data ;
		$result["info"] = "没有数据" ;
		$result["updatedTime"] = time();
		$this->ajaxReturn ($result,'JSON');
	}
	
	//标记超过心跳时间的数据type=0表示丢失通信
	private function handleSafeTime($data){
		$length = count($data);				//取出数组长度，方便遍历数组
		for($i=0 ; $i<$length ; $i++){
			
				$validtime = strtotime($data[$i]['validtime']);		//取出井盖的安全时间期限，即最后有效的时间，超过时间即过期
				$gap_time = time() - $validtime ;
				if($gap_time > 0){									//若间隔时间是正的，代表已经过期
					$data[$i]['type']=0;							//更改为过期，0通信丢失

			}
		}
		return $data ;
	}
	
}