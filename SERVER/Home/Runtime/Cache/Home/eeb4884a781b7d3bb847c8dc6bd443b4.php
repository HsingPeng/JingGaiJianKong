<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="zh-cn">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

	<title>用户管理</title>

	<link rel="stylesheet" href="<?php echo (MEDIA_URL); ?>bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo (MEDIA_URL); ?>bootstrap/css/jquery.dataTables.min.css">

	<link rel="stylesheet" href="<?php echo (MEDIA_URL); ?>Home/css/manage_user.css" class="stylesheet">

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

	<div id="table_wrapper" class=" container dataTables_wrapper" role="grid">
		<div class="row-fluid">
			<div class="span6 myBtnBox"><a id="addFun" class="btn btn-primary">新增</a>
			</div>
			<!--<br>-->
		</div>
		<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-hover" id="table">
			<thead>
				<tr>
					<th>用户ID</th>
					<th>用户名</th>
					<th>备注信息</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
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

	<!-- Modal -->
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="myModalLabel"><span>添加</span></h4>
				</div>
				<div class="modal-body">

					<form class="form-horizontal">

						<div class="form-group">
							<label for="uid" class="col-sm-2 control-label">用户ID</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="uid" placeholder="系统自动生成" disabled="disabled">
							</div>
						</div>

						<div class="form-group">
							<label for="uname" class="col-sm-2 control-label">用户名</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="uname" placeholder="请输入至少4位，不可与其他用户重复">
							</div>
						</div>

						<div class="form-group">
							<label for="upassword" class="col-sm-2 control-label">密码</label>
							<div class="col-sm-10">
								<div class="input-group">
									<span class="input-group-addon">
											<input type="checkbox" id="checkbox_pwd" aria-label="checkbox for upassword">
										</span>
									<input type="password" id="upassword" class="form-control" aria-label="upassword" placeholder="勾选后可修改密码">
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="remark" class="col-sm-2 control-label">备注信息</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="remark" placeholder="请输入">
							</div>
						</div>

					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
					<button type="button" class="btn btn-primary" id="modal-confirm">确定</button>
				</div>
			</div>
		</div>
	</div>
	<!-- /.modal -->

</body>

<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>Home/js/manage_user.js"></script>

<script>
	jQuery(document).ready(init());
</script>

</html>