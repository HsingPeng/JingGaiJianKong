<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends BaseController {
	
    public function index(){
        $this->display();
    }
	
	public function index_map(){
        $this->display();
    }
	
	public function manage_jg(){
		$this->display();
	}
	public function manage_user(){
		$this->display();
	}
	
	public function manage_setting(){
		$this->display();
	}
	
	public function history(){
		$this->display();
	}
	
}