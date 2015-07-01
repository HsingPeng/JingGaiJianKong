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
		
		$db_name = I("post.name",'','string');
		if($db_name == ''){
			$result["data"] = '数据库名不能为空';
			$this->ajaxReturn($result, 'JSON');
		}
		
		//通过临时配置创建数据库，实际通过数据库information_schema操作
		$DB_CONFIG1 = array(
			'DB_TYPE'   			=> C('DB_TYPE'),
			'DB_USER'  				=> C('DB_USER'),
			'DB_PWD'   				=> C('DB_PWD'),
			'DB_PREFIX' 			=> C('DB_PREFIX'),
			'DB_DSN'    			=> 'mysql:host=localhost;dbname=information_schema;charset=utf8'
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
  `describe` varchar(255) DEFAULT NULL COMMENT '设备描述',
  `type` tinyint(4) NOT NULL COMMENT '1：心跳 2：倾斜报警',
  `volt` float NOT NULL COMMENT '电压',
  `angle` int(11) NOT NULL COMMENT '角度',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '通信时间',
  `validtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '心跳有效时间'
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
 ALTER TABLE `equipment`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `number` (`number`);
ALTER TABLE `equipment`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '设备ID',AUTO_INCREMENT=10;
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
ADD CONSTRAINT `eid` FOREIGN KEY (`id`) REFERENCES `equipment` (`id`);
INSERT INTO `admin` (`uid`, `uname`, `upassword`, `kind`, `remark`) VALUES
(100, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 1, '超级');";
		
		return $tables;
	}
	
	private function getDATA(){
		$data = "INSERT INTO `admin` (`uid`, `uname`, `upassword`, `kind`, `remark`) VALUES
(101, 'user01', 'e10adc3949ba59abbe56e057f20f883e', 2, '普通'),
(102, 'user02', 'e10adc3949ba59abbe56e057f20f883e', 2, '普通管理员'),
(103, 'user03', 'e10adc3949ba59abbe56e057f20f883e', 2, '');
INSERT INTO `equipment` (`id`, `number`, `lng`, `lat`, `address`, `safetime`, `uid`, `describe`, `type`, `volt`, `angle`, `time`, `validtime`) VALUES
(1, '13797741868', 116.417854, 39.921988, '北京市东城区王府井大街', 259200, 2, '', 1, 3.58, 0, '2015-06-29 08:40:44', '2015-06-27 16:40:20'),
(2, '13797741867', 116.406605, 39.921585, '北京市东城区东华门大街', 259100, 3, '', 1, 3.6, 0, '2015-06-29 08:41:13', '2015-06-30 08:40:20'),
(3, '13797741866', 116.412222, 39.912345, '北京市东城区东华门大街', 259200, 2, '', 1, 3.58, 0, '2015-06-26 14:30:26', '2015-06-26 08:40:20'),
(4, '17365342763', 116.468375, 39.914296, '北京市, 北京市, 朝阳区, 三环', 259200, 2, '', 0, 0, 0, '2015-06-26 08:40:20', '0000-00-00 00:00:00'),
(5, '15555215556', 116.47628, 39.903725, '北京市, 北京市, 朝阳区, 百子湾路', 259200, 2, 'fsaf', 1, 3.5, 0, '2015-06-30 09:25:39', '2015-07-03 09:25:39'),
(10, '152376457', 116.439629, 39.915348, '北京市, 北京市, 东城区, 建国门内大街,5号', 258900, 2, '', 0, 0, 0, '2015-06-26 14:57:30', '2015-06-30 08:40:20'),
(11, '16583748574', 116.456517, 39.915458, '北京市, 北京市, 朝阳区, 东大桥路,61号', 259100, 3, '', 2, 3, 5, '2015-06-29 09:58:10', '2015-06-29 16:00:00');
INSERT INTO `record` (`rid`, `id`, `type`, `volt`, `angle`, `time`) VALUES
(6, 11, 1, 3.32, 0, '2015-06-28 15:31:03'),
(7, 5, 2, 0, 4, '2015-06-16 17:46:39'),
(8, 5, 1, 3.32, 0, '2015-06-16 17:46:58'),
(9, 5, 2, 0, 4, '2015-06-16 17:52:00'),
(10, 5, 1, 3.32, 0, '2015-06-16 17:57:34'),
(11, 5, 2, 0, 4, '2015-06-16 18:06:16'),
(12, 5, 1, 3.32, 0, '2015-06-16 18:07:14'),
(13, 5, 2, 0, 4, '2015-06-16 18:15:39'),
(14, 5, 1, 3.32, 0, '2015-06-16 18:21:38'),
(15, 5, 2, 0, 4, '2015-06-16 18:27:36'),
(16, 5, 1, 3.32, 0, '2015-06-16 18:27:52'),
(18, 5, 1, 3.32, 0, '2015-06-16 18:30:47'),
(19, 5, 1, 3.32, 0, '2015-06-16 18:32:08'),
(20, 5, 2, 0, 4, '2015-06-16 18:35:45'),
(21, 5, 1, 3.32, 0, '2015-06-16 18:36:33'),
(22, 5, 2, 0, 57, '2015-06-16 18:37:49'),
(23, 5, 1, 3.15, 0, '2015-06-16 18:38:08'),
(24, 5, 2, 0, 57, '2015-06-17 12:07:29'),
(25, 5, 1, 3.15, 0, '2015-06-29 16:58:24'),
(26, 5, 2, 0, 57, '2015-06-29 17:00:52'),
(27, 5, 1, 3.15, 0, '2015-06-29 17:01:09'),
(28, 5, 2, 0, 57, '2015-06-29 17:01:52'),
(29, 5, 1, 3.15, 0, '2015-06-29 17:02:21'),
(30, 5, 1, 3.15, 0, '2015-06-29 17:12:46'),
(54, 5, 1, 3.5, 0, '2015-06-30 09:25:39');";
		return $data;
	}
	
}