<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="zh-cn">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

	<title>登陆</title>

	<link rel="stylesheet" href="<?php echo (MEDIA_URL); ?>bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo (MEDIA_URL); ?>Home/css/login.css" class="stylesheet">

	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/holder.min.js"></script>
	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/md5.js"></script>
	
	<link rel="shortcut icon" href="<?php echo (MEDIA_URL); ?>Home/images/favicon.ico" />
	
</head>

<body>


	<div id="background">
	</div>


	<h1 class="">井盖监控系统</h1>


	<form action="<?php echo (APP_URL); ?>/Home/Login/check" method="post" class="form-horizontal">

		<div class="login">
			<div class="input-group input-group-lg">
				<span class="input-group-addon glyphicon glyphicon-user"></span>
				<input id="username" type="text" name="uname" class="form-control" placeholder="请输入用户名或ID">
			</div>
			<br>
			<div class="input-group input-group-lg">
				<span class="input-group-addon glyphicon glyphicon-lock"></span>
				<input id="password" type="password" name="upassword" class="form-control" placeholder="请输入密码">
			</div>
			<br>

			<div class="input-group input-group-lg">
				<input id="verify" type="text" name="verify" class="form-control" placeholder="请输入验证码">
				<span class="input-group-addon verify">
					<img src="<?php echo (APP_URL); ?>?m=Home&c=Login&a=verify" alt="验证码图片" onclick="this.src='<?php echo (APP_URL); ?>?m=Home&c=Login&a=verify&abc='+Math.random()">
				</span>

			</div>

			<br>

			<div class="">
				<button id="submit"type="submit" class="btn btn-primary btn-lg btn-block">登陆</button>
			</div>
		</div>



	</form>

</body>

<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>Home/js/login.js"></script>

<script>
	jQuery(document).ready(init());
</script>

</html>