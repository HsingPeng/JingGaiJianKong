<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="zh-cn">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

	<title>测试</title>

	<link rel="stylesheet" href="<?php echo (MEDIA_URL); ?>bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo (MEDIA_URL); ?>bootstrap/css/docs.min.css">

	<style type="text/css">
		body,
		html {
			width: 100%;
			height: 100%;
			font-family: "Helvetica Neue", Helvetica, Microsoft Yahei, Hiragino Sans GB, WenQuanYi Micro Hei, sans-serif;
		}
	</style>

	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/bootstrap.min.js"></script>
	<!--[if lt IE 9]>
	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/json2.js"></script>
	<![endif]-->
	<link rel="shortcut icon" href="<?php echo (MEDIA_URL); ?>Home/images/favicon.ico" />

</head>

<body>

	<div class="container">

		<div class="container">
			<h1>模拟更新数据</h1>
		</div>

		<div class="container col-md-6">
			<div class="panel panel-success">
				<div class="panel-heading">更新心跳数据</div>
				<div class="panel-body">
					<form class="form-horizontal">

						<div class="form-group">
							<label for="N_number" class="col-sm-2 control-label">手机号</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="N_number" placeholder="不加+86" onkeyup="value=this.value.replace(/\D+/g,'')">
							</div>
						</div>

						<div class="form-group">
							<label for="N_angle" class="col-sm-2 control-label">倾斜角度</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="N_angle" placeholder="请输入" onkeyup="value=this.value.replace(/\D+/g,'')">
							</div>
						</div>
						
						<div class="form-group">
							<label for="N_volt" class="col-sm-2 control-label">电池电压</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="N_volt" placeholder="请输入">
							</div>
						</div>

					</form>
				</div>
				<div class="panel-footer">
					<button type="button" class="btn btn-success" id="N_confirm">提交</button>
				</div>
			</div>
		</div>

		<div class="container col-md-6">
			<div class="panel panel-danger">
				<div class="panel-heading">更新警告数据</div>
				<div class="panel-body">
					<form class="form-horizontal">

						<div class="form-group">
							<label for="W_number" class="col-sm-2 control-label">手机号</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="W_number" placeholder="不加+86" onkeyup="value=this.value.replace(/\D+/g,'')">
							</div>
						</div>

						<div class="form-group">
							<label for="W_angle" class="col-sm-2 control-label">倾斜角度</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="W_angle" placeholder="请输入" onkeyup="value=this.value.replace(/\D+/g,'')">
							</div>
						</div>

					</form>
				</div>
				<div class="panel-footer">
					<button type="button" class="btn btn-danger" id="W_confirm">提交</button>
				</div>
			</div>
		</div>

	</div>

</body>

<script>
	jQuery(document).ready(init());

	function init() {
		addButton();
	}

	function addButton() {
		$('#N_confirm').click(function () {
			if (check_N()) {
				send_N();
			}
		});

		$('#W_confirm').click(function () {
			if (check_W()) {
				send_W();
			}
		});
	}

	function check_N() {
		var flag = true;
		if ($('#N_number').val() == '') {
			$('#N_number').focus();
			flag = false;
		} else if ($('#N_angle').val() == '') {
			$('#N_angle').focus();
			flag = false;
		} else if ($('#N_volt').val() == '') {
			$('#N_volt').focus();
			flag = false;
		}

		return flag;
	}

	function send_N() {
		$.ajax({
			url: 'home.php?m=Home&c=UpDate&a=update',
			data: JSON.stringify({
				type: "1",
				number: $('#N_number').val(),
				angle: $('#N_angle').val(),
				volt: $('#N_volt').val(),
				time: new Date().format("yyyy-MM-dd hh:mm:ss"),
			}),
			type: 'post',
			contentType: "application/json; charset=utf-8",
			cache: false,
			dataType: 'json',
			success: function (msg) {

				if (msg.data == "success") {
					alert("更新成功！");
				} else {
					alert("更新失败！ " + msg.data);
				}
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				alert("服务器连接出错！ " + textStatus + " " + errorThrown);
			}
		});
	}

	function check_W() {
		var flag = true;
		if ($('#W_number').val() == '') {
			$('#W_number').focus();
			flag = false;
		} else if ($('#W_angle').val() == '') {
			$('#W_angle').focus();
			flag = false;
		}

		return flag;
	}

	function send_W() {
		$.ajax({
			url: 'home.php?m=Home&c=UpDate&a=update',
			data: JSON.stringify({
					type: 2,
					number: $('#W_number').val(),
					angle: $('#W_angle').val(),
					time: new Date().format("yyyy-MM-dd hh:mm:ss"),
				}),
			type: 'post',
			contentType: "application/json; charset=utf-8",
			cache: false,
			dataType: 'json',
			success: function (msg) {

				if (msg.data == "success") {
					alert("更新成功！");
				} else {
					alert("更新失败！ " + msg.data);
				}
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				alert("服务器连接出错！ " + textStatus + " " + errorThrown);
			}
		});
	}

	Date.prototype.format = function (format) {
		var o = {
			"M+": this.getMonth() + 1, //month 
			"d+": this.getDate(), //day 
			"h+": this.getHours(), //hour 
			"m+": this.getMinutes(), //minute 
			"s+": this.getSeconds(), //second 
			"q+": Math.floor((this.getMonth() + 3) / 3), //quarter 
			"S": this.getMilliseconds() //millisecond 
		}

		if (/(y+)/.test(format)) {
			format = format.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
		}

		for (var k in o) {
			if (new RegExp("(" + k + ")").test(format)) {
				format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
			}
		}
		return format;
	}
</script>

</html>