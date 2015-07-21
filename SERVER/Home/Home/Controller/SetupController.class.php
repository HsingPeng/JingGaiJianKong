<?php
namespace Home\Controller;
use Think\Controller;

class SetupController extends LocalController {
	
	public function setup(){
		session('[destroy]');
		cookie("PHPSESSID",null);
		$this->display();
	}
	
	public function step_1(){
		
		$db_dsn = C('DB_DSN');
		preg_match('/dbname=(.*);/i', $db_dsn, $arr);		//正则匹配取出数据库名
		$db_dsn1 = preg_replace('/dbname=(.*);/i','information_schema',$db_dsn);		//替换成临时数据库
		if($arr == null){
			preg_match('/dbname=((?!;).*)$/i', $db_dsn, $arr);		//当字段在最后没有；结尾的情况
			$db_dsn1 = preg_replace('/dbname=((?!;).*)$/i','information_schema',$db_dsn);
		}
		$db_name = $arr[1];
		
		//通过临时配置创建数据库，实际通过数据库information_schema操作
		$DB_CONFIG1 = array(
			'DB_TYPE'   			=> C('DB_TYPE'),
			'DB_USER'  				=> C('DB_USER'),
			'DB_PWD'   				=> C('DB_PWD'),
			'DB_PREFIX' 			=> C('DB_PREFIX'),
			'DB_DSN'    			=> $db_dsn1
		);
		
		$Model = new \Think\Model(null,'',$DB_CONFIG1);
		$Model->execute("DROP DATABASE IF EXISTS %s",$db_name);
		$Model->execute("CREATE DATABASE IF NOT EXISTS %s DEFAULT CHARSET utf8 COLLATE utf8_general_ci",$db_name);
		
		$Model1 = new \Think\Model();
		$Model1->execute("show tables");
		
		$result["data"] = 'success';
		$this->ajaxReturn($result, 'JSON');
	}
	
	public function step_2(){
		
		$inputData = I('post.inputdata',false,'boolean');
		
		$Model = new \Think\Model();
		$Model->execute($this->getTABLES());
		
		if($inputData){
			$Model->execute($this->getDATA());
		}
		
		$result["data"] = 'success';
		$this->ajaxReturn($result, 'JSON');
	}
	
	public function step_3(){

	}
	
	private function getTABLES(){
		
		$tables = "DROP TABLE IF EXISTS `record`;DROP TABLE IF EXISTS `admin`;DROP TABLE IF EXISTS `equipment`;
CREATE TABLE IF NOT EXISTS `admin` (
`uid` int(11) NOT NULL COMMENT '用户ID',
  `uname` varchar(50) NOT NULL COMMENT '用户名',
  `upassword` varchar(50) NOT NULL COMMENT '密码',
  `kind` int(11) NOT NULL COMMENT '1 超级管理员 2 管理员',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注信息'
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
ALTER TABLE `admin`
 ADD PRIMARY KEY (`uid`), ADD UNIQUE KEY `uname` (`uname`);
ALTER TABLE `admin`
MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID',AUTO_INCREMENT=100;
 CREATE TABLE IF NOT EXISTS `equipment` (
`id` int(11) NOT NULL COMMENT '设备ID',
  `number` varchar(13) NOT NULL COMMENT '手机号',
  `lng` double NOT NULL COMMENT '经度',
  `lat` double NOT NULL COMMENT '纬度',
  `address` varchar(255) NOT NULL COMMENT '地址',
  `safetime` int(11) NOT NULL COMMENT '安全间隔时间，秒',
  `uid` int(11) NOT NULL COMMENT '对应管理员',
  `maxangle` int(11) NOT NULL COMMENT '正常角度上限',
  `minangle` int(11) NOT NULL COMMENT '正常角度下限',
  `describe` varchar(255) DEFAULT NULL COMMENT '设备描述',
  `type` tinyint(4) NOT NULL COMMENT '1：心跳 2：倾斜报警',
  `volt` float NOT NULL COMMENT '电压',
  `angle` int(11) NOT NULL COMMENT '角度',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '通信时间',
  `validtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '心跳有效时间'
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
 ALTER TABLE `equipment`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `number` (`number`);
ALTER TABLE `equipment`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '设备ID',AUTO_INCREMENT=10;
ALTER TABLE `equipment`
ADD CONSTRAINT `uid` FOREIGN KEY (`uid`) REFERENCES `admin` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
CREATE TABLE IF NOT EXISTS `record` (
`rid` int(11) NOT NULL COMMENT '记录编码 自增长',
  `id` int(4) NOT NULL COMMENT '设备ID',
  `type` int(1) NOT NULL COMMENT '类型1：心跳 2：倾斜报警',
  `volt` float NOT NULL COMMENT '电压',
  `angle` int(1) NOT NULL COMMENT '角度',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '接收时间'
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;
ALTER TABLE `record`
 ADD PRIMARY KEY (`rid`), ADD KEY `eid` (`id`);
 ALTER TABLE `record`
MODIFY `rid` int(11) NOT NULL AUTO_INCREMENT COMMENT '记录编码 自增长';
ALTER TABLE `record`
ADD CONSTRAINT `eid` FOREIGN KEY (`id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
INSERT INTO `admin` (`uid`, `uname`, `upassword`, `kind`, `remark`) VALUES
(100, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 1, '超级');";
		
		return $tables;
	}
	
	private function getDATA(){
		$data = "INSERT INTO `admin` (`uid`, `uname`, `upassword`, `kind`, `remark`) VALUES 
(101, 'user01', 'e10adc3949ba59abbe56e057f20f883e', 2, '普通'),
(102, 'user02', 'e10adc3949ba59abbe56e057f20f883e', 2, '普通管理员'),
(103, 'user03', 'e10adc3949ba59abbe56e057f20f883e', 2, '');
INSERT INTO `equipment` (`id`, `number`, `lng`, `lat`, `address`, `safetime`, `uid`, `maxangle`, `minangle`, `describe`, `type`, `volt`, `angle`, `time`, `validtime`) VALUES
(1, '15243789087', 118.799442, 32.046679, '江苏省, 南京市, 秦淮区, 中山东路,60号', 259200, 101, 15, 0, 'dfdsa', 1, 3.5, 1, '2015-07-18 09:16:14', '2015-07-22 16:00:00'),
(2, '17535806326', 118.799155, 32.047659, '江苏省, 南京市, 玄武区, 中山东路,147号703室', 259200, 101, 15, 0, '', 0, 0, 0, '2015-07-18 09:16:20', '2015-07-18 16:00:00'),
(5, '15555215556', 118.806269, 32.049679, '江苏省, 南京市, 玄武区, 东箭道', 259200, 101, 18, 1, '', 2, 3.5, 21, '2015-07-18 09:14:46', '2015-07-20 07:49:45');
INSERT INTO `record` (`rid`, `id`, `type`, `volt`, `angle`, `time`) VALUES
(1, 5, 1, 0, 2, '2015-07-18 07:48:18'),
(2, 5, 1, 3.5, 0, '2015-07-18 07:49:45'),
(3, 5, 1, 0, 18, '2015-07-18 07:50:13'),
(4, 5, 2, 0, 22, '2015-07-18 07:50:31'),
(5, 5, 1, 0, 11, '2015-07-18 07:50:39');";
		return $data;
	}
	
}