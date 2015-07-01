//初始化
function init() {
	initNavBar();
	show_load_model();

	TABLE_COUNT = initCount(); //表格里异常数据计数器

	TOP_TABLE = initTopTable();		//初始化表格

	CONFIG = new Object();		//创建一个变量，存储一些配置
	CONFIG.init = 1; //默认第一次是初始化请求
	CONFIG.updatedTime = ''; //上次更新的最新时间
	CONFIG.status = 1; //0为意外断线

	getCurrent(); //开始获取数据
	setConnectStatus(2); //设置标签为正在连接

}

//点亮导航条位置
function initNavBar() {
	$('#nav_index').addClass('active');
}

function initTopTable() {

	var table_init = $('#top_table').DataTable({
		"oLanguage": { // 汉化
			"sProcessing": "正在加载数据...",
			"sLengthMenu": "显示 _MENU_ 条 ",
			"sZeroRecords": "没有异常数据",
			"sInfo": "当前从_START_ 到 _END_ 条记录——总记录数为 _TOTAL_ 条",
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
		"bFilter": false, // 搜索栏
		"bAutoWidth": false, //一行显示
		"bLengthChange": false, // 每页显示记录数
		"deferRender": true, //延迟加载
		"columns": [
			{
				"data": "id"
			},
			{
				"data": "address"
			},
			{
				"data": "type"
			},
			{
				"data": "angle"
			},
			{
				"data": "volt"
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
      }],
	});
	return table_init;
}

//显示等待窗口
function show_load_model() {
	//模态 窗口
	$('#load_modal').modal({
		show: true, //显示
		keyboard: false, //键盘ESC不可用
		backdrop: 'static' //点击空白处不可关闭
	});
}

function getCurrent() {

	$.ajax({
		type: "POST",
		dataType: "json",
		url: "home.php?m=Home&c=GetCurrent&a=getcurrent",
		data: {
			data: JSON.stringify(CONFIG)
		}, //数据转成JSON发送出去
		timeout: 30000, //超时时间
		success: function (msg) {

			CONFIG.status = 1;			//设置状态为正常连接
			setConnectStatus(1);		//标签设置为已连接

			try {

				//当返回0时表示登陆失效，跳转到登陆界面
				if (msg.status === 0) {
					location.href = msg.url;
					return;
				}

				var data = msg.data;

				if (data.length > 0) {		//判断是否有数据
					for (var y in data) {
						var x = data[y];
						//添加
						addItem(x["id"] * 1, x["number"] * 1, x["lat"] * 1, x["lng"] * 1, x["address"], x["type"] * 1, x["time"], x["angle"] * 1, x["volt"] * 1, x["describe"]);
					}

					//是否是第一次请求
					if (CONFIG.init) {
						CONFIG.init = 0; //关闭初始化请求
					}

				}


				//表格里是否有数据，没有数据则说明没有异常
				if (TABLE_COUNT.count === 0) {
					changeStatus(1); //设置状态正常
				} else {
					changeStatus(2); //设置状态异常
				}

				CONFIG.updatedTime = msg.updatedTime;		//更新上次通信的时间，方便下次进行对比，拿到这段时间新的数据

			} catch (e) {
				alert("获取数据错误: " + e.name + ' ' + e.message);
			}

			getCurrent();			//发起新的连接
			$('#load_modal').modal('hide');		//隐藏等待窗口

		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			//alert("连接服务器错误: " + textStatus + " " + errorThrown);
			setConnectStatus(3);		//标签设置为断开连接
			CONFIG.status = 0;			//状态设置为意外断线
			$('#load_modal').modal('hide');		//隐藏等待窗口
			//设置定时重试
			setTimeout(function () {
				getCurrent();
				setConnectStatus(2);		//标签设置为正在连接
			}, 1000);
		}
	});
}

//设置服务器状态 1 已连接 2 重新连接 3 已断开
function setConnectStatus(kind) {

	$('#connect_status').removeClass('label-default label-success label-warning label-danger');


	switch (kind) {
	case 1:
		$('#connect_status').addClass("label-success");
		$('#connect_status').text('已连接服务器');
		break;
	case 2:
		$('#connect_status').addClass("label-warning");
		$('#connect_status').text('正在连接服务器');
		break;
	case 3:
		$('#connect_status').addClass("label-danger");
		$('#connect_status').text('已断开服务器');
		break;

	}

}

/*载入项*/
function addItem(id, number, lat, lng, address, type, time, angle, volt, describe) {


	var flag = true; //是否不存在的标志,防止重复添加

	if (!CONFIG.init) {
		//遍历对比是否有重复井盖
		TOP_TABLE.rows().every(function () {
			var data = this.data();
			if (data!=null && data.id == id) {

				//如果由异常变成正常，则删除该行
				if (type === 1) {
					this.remove().draw();
					TABLE_COUNT.reduce();
				} else {
					data.address = address;
					data.type = type;
					data.angle = angle;
					data.volt = volt;
					data.time = time;
					flag = false;

					TOP_TABLE.row(this).data(data).draw(false); //更新数据
				}
			}
		});
	}

	//如果井盖是正常状态则不添加，结束方法
	if (type === 1) {
		return;
	}

	if (flag) {
		TOP_TABLE.row.add({
			id: id,
			address: address,
			type: type,
			angle: angle,
			volt: volt,
			time: time
		}).draw();
		TABLE_COUNT.add();
	}



}

//初始化表格计数器，用于判断井盖是否有异常
function initCount() {

	return {
		count: 0,
		add: function () {
			this.count++;
		},
		reduce: function () {
			if (this.count != 0) {
				this.count--;
			}
		}
	};

}

//1 为正常 2为异常
function changeStatus(type) {
	if (type === 1) {
		$('#status_ok').show();
		$('#status_error').hide();
	} else if (type === 2) {
		$('#status_ok').hide();
		$('#status_error').show();
	}
}