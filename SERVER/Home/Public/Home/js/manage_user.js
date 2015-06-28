/*初始化数据*/
function init() {
	initNavBar();
	TABLE = initTable();
	MODAL = 0;
	addButton();
	addClick();
}

//点亮导航条位置
function initNavBar(){
	$('#nav_manage').addClass('active');
	$('#nav_manage_user').addClass('active');
}

function addClick() {
	$('#checkbox_pwd').change(function () {
		if (this.checked) {
			$('#upassword').attr("disabled", false);
		} else {
			$('#upassword').attr("disabled", true);
			$('#upassword').val('');
		}
	});

	$('#table tbody').on('click', '#editFun', function () {

		MODAL = 2;

		var row = TABLE.row($(this).closest('tr'));
		//alert(row.data().id);

		$('#uid').val(row.data().uid);
		$('#uname').val(row.data().uname);
		$('#checkbox_pwd').attr('checked', false);
		$('#checkbox_pwd').attr("style", "visibility: visible");
		$('#upassword').val('');
		$("#upassword").attr("disabled", true);
		$("#upassword").attr("placeholder","勾选后可修改密码");
		$('#remark').val(row.data().remark);

		$('#myModalLabel').text('修改用户信息');
		//打开模态窗口
		$('#myModal').modal({
			show: true, //显示
			keyboard: false, //键盘ESC
			backdrop: 'static' //点击空白处不可关闭
		});


	});

	$('#table tbody').on('click', '#deleteFun', function () {
		var row = TABLE.row($(this).closest('tr'));
		var r = confirm("确认删除用户ID为" + row.data().uid + '的用户！');
		if (r == true) {
			sendDelete(row.data().uid);
		}

	});

}

function addButton() {

	//确认按钮
	$('#modal-confirm').click(function () {
		if (MODAL === 1) {
			//alert("添加");
			sendCreate();
		} else if (MODAL === 2) {
			//alert("修改");
			sendUpdate();
		}
	});

	$('#addFun').click(function () {

		MODAL = 1;

		var uid = $("#USER_ID").attr("name");

		$('#uid').val('系统自动生成');
		$('#uname').val('');
		$('#checkbox_pwd').attr('checked', true);
		$('#checkbox_pwd').attr("style", "visibility: hidden");
		$('#upassword').val('');
		$("#upassword").attr("disabled", false);
		$("#upassword").attr("placeholder","请输入");
		$('#remark').val('');

		$('#myModalLabel').text('添加用户');
		//打开模态窗口
		$('#myModal').modal({
			show: true, //显示
			keyboard: false, //键盘ESC
			backdrop: 'static' //点击空白处不可关闭
		});

	});
}

function sendCreate() {
	var flag = checkInput();
	if (flag) {

		$('#load_modal').modal({
			show: true, //显示
			keyboard: false, //键盘ESC
			backdrop: 'static' //点击空白处不可关闭
		});

		$.ajax({
			url: 'home.php?m=Home&c=ManageUser&a=create',
			data: {
				uname: $('#uname').val(),
				upassword: hex_md5($('#upassword').val()),
				remark: $('#remark').html(),
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
					$('#myModal').modal('hide');	//隐藏load窗口
					TABLE.ajax.reload();		//表格重新加载数据
					alert("添加成功！用户ID为："+msg.id);
				} else {
					alert("添加失败！ " + msg.data);
				}
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				$('#load_modal').modal('hide');
				alert("服务器连接出错！ " + textStatus + " " + errorThrown);
			}
		});
	}
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
		if($('#upassword').val()!=''){
			upwd = hex_md5($('#upassword').val());
		}

		$.ajax({
			url: 'home.php?m=Home&c=ManageUser&a=update',
			data: {
				uid: $('#uid').val(),
				uname: $('#uname').val(),
				upassword: upwd,
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
					$('#myModal').modal('hide');
					TABLE.ajax.reload();		//表格重新加载数据
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

function sendDelete(uid) {
	$('#load_modal').modal({
		show: true, //显示
		keyboard: false, //键盘ESC
		backdrop: 'static' //点击空白处不可关闭
	});

	$.ajax({
		url: 'home.php?m=Home&c=ManageUser&a=delete',
		data: {
			uid: uid,
		},
		type: 'post',
		cache: false,
		dataType: 'json',
		success: function (msg) {

			//当返回0时表示登陆失效，跳转到登陆界面
			if (msg.status === 0) {
				location.href = msg.url;
				return;
			}

			$('#load_modal').modal('hide');

			if (msg.data == "success") {
				TABLE.ajax.reload(); //表格重新加载数据
				alert("删除成功！");
			} else {
				alert("删除失败！ " + msg.data);
			}
		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			$('#load_modal').modal('hide');
			alert("服务器连接出错！ " + textStatus + " " + errorThrown);
		}
	});
}

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
	}

	return flag;
}

/**
 * 表格初始化
 * @returns {*|jQuery}
 */
function initTable() {
	var table = $("#table").DataTable({

		"sAjaxSource": 'home.php?m=Home&c=ManageUser&a=retrieve',
		"oLanguage": { // 汉化
			"sProcessing": "正在加载数据...",
			"sLengthMenu": "每页显示 _MENU_ 条 ",
			"sZeroRecords": "没有内容",
			"sInfo": "从_START_ 到 _END_ 条记录——总记录数为 _TOTAL_ 条",
			"sInfoEmpty": "记录数为0",
			"sInfoFiltered": "(全部记录数 _MAX_  条)",
			"sInfoPostFix": "",
			"sSearch": "搜索",
			"sUrl": "",
			"oPaginate": {
				"sFirst": "第一页",
				"sPrevious": " 上一页 ",
				"sNext": " 下一页 ",
				"sLast": " 最后一页 "
			}
		},
		"iDisplayLength": 10, // 每页显示行数
		"bAutoWidth": false, //一行显示
		"columns": [
			{
				"data": "uid"
			},
			{
				"data": "uname"
			},
			{
				"data": "remark"
			}
        ],
		"columnDefs": [{
			"render": function (data, type, row) {
				return '<a href="#" class="btn btn-primary" id="editFun">修改</a> ' + '&nbsp;' + '<a href="#" class="btn btn-danger" id="deleteFun">删除</a>' + '&nbsp;';
			},
			"targets": 3
      }],
	});
	return table;
}