<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="zh-cn">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

	<title>历史记录</title>

	<link rel="stylesheet" href="<?php echo (MEDIA_URL); ?>bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo (MEDIA_URL); ?>bootstrap/css/jquery.dataTables.min.css">
	<link rel="stylesheet" href="<?php echo (MEDIA_URL); ?>Home/css/history.css" class="stylesheet">

	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/holder.min.js"></script>
	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/jquery.dataTables.min.js"></script>

</head>

<body>

	<!-- 模态框（Modal） -->
	<div class="modal" id="load_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div class="progress" style="margin-bottom: 0px">
						<div class="progress-bar progress-bar-striped progress-bar-success active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
							<!--<span class="sr-only">-->正在初始化...
							<!--</span>-->
						</div>
					</div>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
	</div>
	<!-- /.modal -->

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
						<li><a href="<?php echo (APP_URL); ?>?m=Home&c=Index&a=index">实时地图</a>
						</li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="true">管理<span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="<?php echo (APP_URL); ?>?m=Home&c=Index&a=manage_jg">井盖管理</a>
								</li>
								<?php if(($_SESSION['kind']) == "1"): ?><li><a href="<?php echo (APP_URL); ?>?m=Home&c=Index&a=manage_user">用户管理</a><?php endif; ?>
								<li><a href="<?php echo (APP_URL); ?>?m=Home&c=Index&a=manage_setting">当前设置</a>
								</li>
							</ul>
							</li>
							<li class="active"><a href="#">历史记录</a>
							</li>
					</ul>
					<ul class="nav navbar-nav navbar-right">
						<li>
							<a>
								<?php if(($_SESSION['kind']) == "1"): ?>超级管理员
									<?php else: ?>普通管理员<?php endif; ?>：<?php echo (session('uname')); ?>&nbsp;&nbsp;ID:<?php echo (session('uid')); ?></a>
						</li>
						<li><a href="<?php echo (APP_URL); ?>?m=Home&c=Login&a=logout">登出</a>
						</li>
					</ul>
				</div>

		</nav>
		</div>

		<!-- /.导航 -->

		<div id="table_wrapper" class=" container dataTables_wrapper" role="grid">
			<div class="row-fluid">
				<div class="span6 myBtnBox"><a id="addFun" class="btn btn-primary">新增</a>
				</div>
				<!--<br>-->
			</div>
			<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-hover" id="table">
				<thead>
					<tr>
						<th>记录编号</th>
						<th>设备ID</th>
						<th>类型</th>
						<th>电压</th>
						<th>角度</th>
						<th>接收时间</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>


</body>

<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>Home/js/history.js"></script>

<script>
	jQuery(document).ready(init());
</script>

</html>