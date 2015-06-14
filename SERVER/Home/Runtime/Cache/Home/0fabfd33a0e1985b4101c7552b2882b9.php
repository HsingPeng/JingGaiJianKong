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


</head>

<body>

	<form class="form-horizontal">

			<h1 class="">井盖监控系统</h1>
			<br>

		<div class="input-group input-group-lg">
			<span class="input-group-addon glyphicon glyphicon-user" id="sizing-addon1"></span>
			<input type="text" class="form-control" placeholder="请输入用户名" aria-describedby="sizing-addon1">
		</div>
		<br>
		<div class="input-group input-group-lg">
			<span class="input-group-addon glyphicon glyphicon-lock" id="sizing-addon1"></span>
			<input type="password" class="form-control" placeholder="请输入密码" aria-describedby="sizing-addon1">
		</div>
		<br>
		<div class="">
			<button type="submit " class="btn btn-primary btn-lg btn-block">登陆</button>
		</div>

	</form>

</body>

<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>Home/js/login.js"></script>

<script>
	jQuery(document).ready(init());
</script>

</html>