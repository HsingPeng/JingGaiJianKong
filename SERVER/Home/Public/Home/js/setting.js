/*初始化数据*/
function init() {

	addClick();
	initNavBar();
	
}

//点亮导航条位置
function initNavBar(){
	$('#nav_setting').addClass('active');
}

function addClick() {

	$('#checkbox_pwd').change(function () {
		if (this.checked) {
			$('#upassword').attr("disabled", false);
			$('#old_password_group').show();
		} else {
			$('#upassword').attr("disabled", true);
			$('#upassword').val('');
			$('#old_password_group').hide();
			$('#old_upassword').val('');
		}
	});
	
	//确认按钮
	$('#admin-confirm').click(function () {
		sendUpdate();
	});
}

function sendUpdate() {
	var flag = checkInput();
	if (flag) {

		$('#load_modal').modal({
			show: true, //显示
			keyboard: false, //键盘ESC
			backdrop: 'static' //点击空白处不可关闭
		});

		var upwd = '' ;
		var old_upwd = '' ;
		if($('#upassword').val()!=''){
			upwd = hex_md5($('#upassword').val());
			old_upwd = hex_md5($('#old_upassword').val());
		}

		$.ajax({
			url: 'home.php?m=Home&c=Setting&a=updateAdmin',
			data: {
				uid: $('#uid').val(),
				uname: $('#uname').val(),
				upassword: upwd,
				old_upassword: old_upwd,
				remark: $('#remark').val(),
			},
			type: 'post',
			cache: false,
			dataType: 'json',
			success: function (msg) {

				//当返回0时表示登陆失效，跳转到登陆界面
				if(msg.status===0){
					location.href = msg.url;
					return;
				}
				
				$('#load_modal').modal('hide');

				if (msg.data == "success") {
					alert("更新成功！");
				} else {
					alert("更新失败！ " + msg.data);
				}
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				alert("服务器连接出错！ " + textStatus + " " + errorThrown);
				$('#load_modal').modal('hide');
			}
		});
	}
}

//检查input是否为空
function checkInput() {
	
	var flag = true;
	if ($('#uid').val() == '') {
		$('#uid').focus();
		flag = false;
	} else if ($('#uname').val() == '') {
		$('#uname').focus();
		flag = false;
	} else if ($('#checkbox_pwd')[0].checked && $('#upassword').val() == '') {
		$('#upassword').focus();
		flag = false;
	} else if($('#checkbox_pwd')[0].checked && $('#old_upassword').val() == '') {
		$('#old_upassword').focus();
		flag = false;
	}

	return flag;
}