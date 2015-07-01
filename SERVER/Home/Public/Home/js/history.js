//初始化
function init() {
	initNavBar();
	TABLE = initTable();
	addButton();
	addClick();
}

//点亮导航条位置
function initNavBar(){
	$('#nav_manage').addClass('active');
	$('#nav_history').addClass('active');
}

function addClick() {
	$('#table tbody').on('click', '#deleteFun', function () {
		var row = TABLE.row($(this).closest('tr'));
		var r = confirm("确认删除记录编号为" + row.data().rid + '的记录！');
		if (r == true) {
			sendDelete(row.data().rid);
		}

	});
}

function addButton() {
	//删除所有按钮
	$('#deleteALL').click(function () {
		var r = confirm("确认删除所有记录？");
		if (r == true) {
			sendDeleteALL();
		}
	});
}

function sendDeleteALL(){
	$('#load_modal').modal({
		show: true, //显示
		keyboard: false, //键盘ESC
		backdrop: 'static' //点击空白处不可关闭
	});

	$.ajax({
		url: 'home.php?m=Home&c=History&a=deleteALL',
		data: {
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

function sendDelete(rid) {
	$('#load_modal').modal({
		show: true, //显示
		keyboard: false, //键盘ESC
		backdrop: 'static' //点击空白处不可关闭
	});

	$.ajax({
		url: 'home.php?m=Home&c=History&a=delete',
		data: {
			rid: rid,
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

/**
 * 表格初始化
 * @returns {*|jQuery}
 */
function initTable() {
	var table = $("#table").DataTable({

		"sAjaxSource": 'home.php?m=Home&c=History&a=retrieve',
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
		"bSort": true, // 排序,
		"order": [[ 0, 'desc' ]],//默认降序
		"columns": [
			{
				"data": "rid"
			},
			{
				"data": "id"
			},
			{
				"data": "type"
			},
			{
				"data": "volt"
			},
			{
				"data": "angle"
			},
			{
				"data": "time"
			}
        ],
		"columnDefs": [{
				"render": function (data, type, row) {
					var status = "丢失通信";
					var color = "default";
					if (data == 1) {
						status = "正常";
						color = "success";
					} else if (data == 2) {
						status = "倾斜报警";
						color = "danger";
					}

					return '<span class="label label-' + color + '">' + status + '</span>';
				},
				"targets": 2
      },
			{
				"render": function (data, type, row) {
					return '<a href="#" class="btn btn-danger" id="deleteFun">删除</a>';
				},
				"targets": 6
      }],
	});
	return table;
}