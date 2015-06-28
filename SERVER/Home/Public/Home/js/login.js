/*初始化数据*/
function init() {
	
	$("#submit").click(function(){
		if(checkInput()){
			$('#password').val(hex_md5($('#password').val()));
			return true;
		}else{
			return false;
		}
	});
}

function checkInput() {
	var flag = true;
	if ($('#username').val() == '') {
		//alert("设备ID未填写");
		$('#username').focus();
		flag = false;
	} else if ($('#password').val() == '') {
		//alert("手机号未填写");
		$('#password').focus();
		flag = false;
	} else if ($('#verify').val() == '') {
		$('#verify').focus();
		flag = false;
	}

	return flag;
}