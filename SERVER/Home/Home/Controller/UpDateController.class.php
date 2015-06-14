<?php
namespace Home\Controller;
use Think\Controller;

class UpDateController extends Controller {
    public function update(){
		
		$time_t = date("Y-m-d H:i:s ",time());
        $Equip = M('Equipment');
			//$data = $Equip->select();
			$data = $Equip->where("time<'%s'",array($time_t))->select();
			echo show_bug($data) ;
		
    }
}