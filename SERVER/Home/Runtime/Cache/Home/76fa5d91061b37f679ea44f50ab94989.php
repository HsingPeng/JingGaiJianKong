<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="zh-cn">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

	<title>监控主页</title>

	<link rel="stylesheet" href="<?php echo (MEDIA_URL); ?>bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo (MEDIA_URL); ?>bootstrap/css/jquery.dataTables.min.css">

	<link rel="stylesheet" href="<?php echo (MEDIA_URL); ?>Home/css/index.css" class="stylesheet">

	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/holder.min.js"></script>
	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/md5.js"></script>
	
	<link rel="shortcut icon" href="<?php echo (MEDIA_URL); ?>Home/images/favicon.ico" />
	
</head>

<body>

	<!--调用导航模板-->
	<!-- 导航 -->
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
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav navbar-left">
					<li id="nav_index"><a href="<?php echo (APP_URL); ?>?m=Home&c=Index&a=index">监控主页</a>
					</li>
					<li id="nav_index_map"><a href="<?php echo (APP_URL); ?>?m=Home&c=Index&a=index_map">实时地图</a>
					</li>
					<li class="dropdown" id="nav_manage">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="true">管理&nbsp;<span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li id="nav_manage_jg"><a href="<?php echo (APP_URL); ?>?m=Home&c=Index&a=manage_jg">井盖管理</a>
							</li>
							<?php if(($_SESSION['kind']) == "1"): ?><li id="nav_manage_user"><a href="<?php echo (APP_URL); ?>?m=Home&c=Index&a=manage_user">用户管理</a><?php endif; ?>
							<li id="nav_history"><a href="<?php echo (APP_URL); ?>?m=Home&c=Index&a=history">历史记录</a>
							</li>
						</ul>
						</li>

				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown" id="nav_admin">
						<a name="<?php echo (session('uid')); ?>" kind="<?php echo (session('kind')); ?>" id="USER_ID" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="true">
							<?php if(($_SESSION['kind']) == "1"): ?>超级管理员
								<?php else: ?>普通管理员<?php endif; ?>：<?php echo (session('uname')); ?>&nbsp;&nbsp;ID:<?php echo (session('uid')); ?>&nbsp;<span class="caret"></span>
						</a>
						<ul class="dropdown-menu" role="menu">
							<li id="nav_setting"><a href="<?php echo (APP_URL); ?>?m=Home&c=Index&a=setting">设置</a>
							</li>
							<li role="separator" class="divider"></li>
							<li><a href="<?php echo (APP_URL); ?>?m=Home&c=Login&a=logout">退出</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>

	</nav>
	</div>

	<!-- /.导航 -->

	<div class="container">

		<div class="connect_status_right"><span class="label label-success text-size-18" id="connect_status">服务器已连接</span>
		</div>

		<div class="highlight">

			<div class="pull-left" id="status_ok" style="display: none;">
				<span class="glyphicon glyphicon-ok-sign"></span>
				<span class="text-wrap">
				井盖状态:
				<span class="text-success text-size-30">一切正常</span>
				</span>
			</div>

			<div class="pull-left" id="status_error" style="display: none;">
				<span class="glyphicon glyphicon-remove-sign"></span>
				<span class="text-wrap">
				井盖状态:
				<span class="text-danger text-size-30">出现异常</span>
				</span>
			</div>

			<!--<div role="separator" class="divider-line"></div>-->

			<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-hover" id="top_table">
				<thead>
					<tr>
						<th>设备ID</th>
						<th>地址</th>
						<th>状态</th>
						<th>角度/°</th>
						<th>电池电压/v</th>
						<th>最近通信时间</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>

		</div>
	</div>



	<!-- 模态框（Modal） -->
	<div class="modal" id="load_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div class="progress" style="margin-bottom: 0px">
						<div class="progress-bar progress-bar-striped progress-bar-success active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
							<!--<span class="sr-only">-->正在传输...
							<!--</span>-->
						</div>
					</div>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
	</div>
	<!-- /.modal -->


</body>

<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>Home/js/index.js"></script>

<script>
	jQuery(document).ready(init());
</script>

</html>