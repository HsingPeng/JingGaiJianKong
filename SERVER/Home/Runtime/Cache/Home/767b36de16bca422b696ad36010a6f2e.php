<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="zh-cn">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

	<title>井盖管理</title>

	<link rel="stylesheet" href="<?php echo (MEDIA_URL); ?>bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo (MEDIA_URL); ?>bootstrap/css/jquery.dataTables.min.css">

	<link rel="stylesheet" href="<?php echo (MEDIA_URL); ?>Home/css/manage_jg.css" class="stylesheet">

	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/holder.min.js"></script>
	<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>bootstrap/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=mCdkQAiYb4a7X2tm78Z2uo3n"></script>
	
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

	<div id="table_wrapper" class=" container-fluid dataTables_wrapper" role="grid">
		<div class="row-fluid">
			<div class="span6 myBtnBox"><a id="addFun" class="btn btn-primary">新增</a>
			</div>
			<!--<br>-->
		</div>
		<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered table-hover" id="table">
			<thead>
				<tr>
					<th>设备ID</th>
					<th>手机号</th>
					<th>经度</th>
					<th>纬度</th>
					<th>地址</th>
					<th>角度上限</th>
					<th>角度下限</th>
					<th>心跳时间</th>
					<th>设备描述</th>
					<th>管理者ID</th>
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
							<label for="input-id" class="col-sm-2 control-label">设备ID</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" id="input-id" placeholder="自动生成">
							</div>
							<label for="safetime" class="col-sm-3 control-label">安全心跳时间(秒)</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="safetime" placeholder="259200 为3天" onkeyup="value=this.value.replace(/\D+/g,'')">
							</div>
						</div>
						<div class="form-group">
							<label for="number" class="col-sm-2 control-label">手机号</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="number" placeholder="请输入" onkeyup="value=this.value.replace(/\D+/g,'')">
							</div>
							<label for="uid" class="col-sm-2 control-label">管理者ID</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="uid" placeholder="请输入" onkeyup="value=this.value.replace(/\D+/g,'')">
							</div>
						</div>
						<div class="form-group">
							<label for="maxangle" class="col-sm-2 control-label">角度上限</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="maxangle" placeholder="例如15" onkeyup="value=this.value.replace(/\D+/g,'')">
							</div>
							<label for="minangle" class="col-sm-2 control-label">角度下限</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="minangle" placeholder="例如0" onkeyup="value=this.value.replace(/\D+/g,'')">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label text-center">百度经度</label>
							<label class="col-sm-4 control-label text-left" id="lng">点击地图自动获取</label>
							<label class="col-sm-2 control-label text-center">百度纬度</label>
							<label class="col-sm-4 control-label text-center" id="lat">点击地图自动获取</label>
						</div>
						<div class="form-group">
							<label for="address" class="col-sm-2 control-label">地址</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="address" placeholder="点击地图自动获取，可修改">
							</div>
						</div>

						<div class="form-group">
							<div id="allmap"></div>
						</div>

						<div class="form-group">
							<label for="describe" class="col-sm-2 control-label">设备描述</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="describe" placeholder="可填写相关信息">
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

<script type="text/javascript" src="<?php echo (MEDIA_URL); ?>Home/js/manage_jg.js"></script>

<script>
	jQuery(document).ready(function () {
		try {
			init();
		} catch (e) {
			if (e.name == "ReferenceError" && e.message.indexOf("BMap") >= 0) {
				var r = confirm("连接百度地图出错,请确保网络正常，点击确认重新加载页面!");
				if (r == true) {
					location.reload();
				}
			} else {
				alert(e.name + ": " + e.message);
			}
		}
	});
</script>

</html>