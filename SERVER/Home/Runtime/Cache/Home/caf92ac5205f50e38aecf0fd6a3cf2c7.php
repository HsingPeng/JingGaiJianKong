<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="zh-cn">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

	<title>用户管理</title>

	<link rel="stylesheet" href="<?php echo (MEDIA_URL); ?>bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo (MEDIA_URL); ?>Home/css/manage_3.css" class="stylesheet">

	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/holder.min.js"></script>

</head>

<body>
	<div class="container">
		<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">

			<div class="container-fluid">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#">井盖监控系统</a>
				</div>

				<!-- Collect the nav links, forms, and other content for toggling -->
				<ul class="nav navbar-nav navbar-left">
					<li><a href="home.php?m=Home&c=Index&a=index">主页</a>
					</li>
					<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="true">管理<span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="home.php?m=Home&c=Index&a=manage_1">井盖管理</a>
								</li>
								<!--<li><a href="home.php?m=Home&c=Index&a=manage_2">接收器管理</a>
								</li>-->
								<li><a href="#">用户管理</a>
								</li>
							</ul>
						</li>
				</ul>

				<ul class="nav navbar-nav navbar-right">
					<li><a href="home.php?m=Home&c=Index&a=login">登出</a>
					</li>
				</ul>

		</nav>
		</div>






</body>

<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>Home/js/manage_3.js"></script>

<script>
	jQuery(document).ready(init());
</script>

</html>