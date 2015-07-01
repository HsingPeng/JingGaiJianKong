/*添加地图上的标记*/
function addMarker(id, lat, lng, info, type) {
	var color = -69;
	var status = "丢失通信";
	if (type === 2) {
		color = -46;
		status = "井盖报警";
	} else if (type === 1) {
		color = 1;
		status = "井盖正常";
	}

	var window_opts = {
		width: 250, // 信息窗口宽度
		height: 80, // 信息窗口高度
		title: "ID:" + id + " " + status, // 信息窗口标题
		enableMessage: false //设置不允许信息窗发送短息
	};

	var marker_opts = {
		icon: new BMap.Icon("http://api.map.baidu.com/lbsapi/createmap/images/icon.png", new BMap.Size(20, 25), {
			imageOffset: new BMap.Size(color, -21)
		})
	};

	var point = new BMap.Point(lng, lat);
	var marker = new BMap.Marker(point, marker_opts); /*创建标注*/
	map.addOverlay(marker); /*将标注添加到地图中*/

	var infoWindow = new BMap.InfoWindow(info, window_opts); /*创建信息窗口对象 */
	itemlist[id]['infowindow'] = infoWindow; //添加到全局变量集合里面
	/*addClickHandler(info, marker, openInfo);*/
	marker.addEventListener("click", function (e) {
		openInfo(infoWindow, e);
	});

	function openInfo(infoWindow, e) {
		var p = e.target;
		var point = new BMap.Point(p.getPosition().lng, p.getPosition().lat);
		map.openInfoWindow(infoWindow, point); /*开启信息窗口*/
	}

};
/*载入项*/
function addItem(id, number, lat, lng, address, type, time, angle, volt, describe) {
	itemlist[id] = {
		id: id,
		number: number,
		lat: lat,
		lng: lng,
		address: address,
		type: type,
		time: time,
		angle: angle,
		volt: volt,
		describe: describe,
		infowindow: null
	};
	addMarker(id, lat, lng, address, type);
	addTable2(id, address, type, angle, volt, time);

}


function addTable2(id, address, type, angle, volt, time) {

	//var status = type == 1 ? "正常" : "倾斜报警";
	var flag = true;


	if (!CONFIG.init) {
		TABLE.rows().every(function () {
			var data = this.data();
			if (data.id == id) {
				data.address = address;
				data.type = type;
				data.angle = angle;
				data.volt = volt;
				data.time = time;
				flag = false;

				TABLE.row(this).data(data).draw(false); //更新数据


				var status = "丢失通信";
				var color = "default";
				if (type == 1) {
					status = "正常";
					color = "success";
				} else if (type == 2) {
					status = "倾斜报警";
					color = "danger";
				}

				var result = '<span class="label label-' + color + '">' + status + '</span>';

				var tr_i = this.index();
				var tr_t = $('tbody').children('tr').eq(tr_i);
				var td_t = tr_t.children('td').eq(2);
				//var t_span = td_t.children('span');
				td_t.html(result);

				//tr_t.children[2].children[0].removeClass('label-default label-success label-warning label-danger');
			}
		});
	}



	if (flag) {
		TABLE.row.add({
			id: id,
			address: address,
			status: type,
			angle: angle,
			volt: volt,
			time: time
		}).draw();
	}

}


/*定位到中心*/
function locateItem(id) {
	if (id != null) {
		var lat_t = itemlist[id]["lat"];
		var lng_t = itemlist[id]["lng"];
		var point = new BMap.Point(lng_t, lat_t);
		map.panTo(point);
		map.openInfoWindow(itemlist[id]["infowindow"], point); /*开启信息窗口*/
	}
}

function getCurrent() {

	$.ajax({
		type: "POST",
		dataType: "json",
		url: "home.php?m=Home&c=GetCurrent&a=getcurrent",
		data: {
			data: JSON.stringify(CONFIG)
		}, //转成JSON发送出去
		timeout: 30000, //超时
		success: function (msg) {

			CONFIG.status = 1;
			setStatus(1);

			try {

				//当返回0时表示登陆失效，跳转到登陆界面
				if (msg.status === 0) {
					location.href = msg.url;
					return;
				}

				var data = msg.data;

				if (data.length > 0) {
					for (var y in data) {
						if (y != null) {
							var x = data[y];

							addItem(x["id"] * 1, x["number"] * 1, x["lat"] * 1, x["lng"] * 1, x["address"], x["type"] * 1, x["time"], x["angle"] * 1, x["volt"] * 1, x["describe"]);
						}
					}

					//把上次更新的项放进去
					if (CONFIG.init) {
						//地图中心定位到第一个item
						var tr = $("tbody tr:first");
						var row = TABLE.row(tr);;
						var id = row.data().id;
						locateItem(id);

						CONFIG.init = 0; //关闭初始化请求

					}
				}


				CONFIG.updatedTime = msg.updatedTime;


			} catch (e) {
				alert("获取数据错误: " + e.name + ' ' + e.message);
			}

			getCurrent();
			$('#load_modal').modal('hide');

		},
		error: function (XMLHttpRequest, textStatus, errorThrown) {
			//alert("连接服务器错误: " + textStatus + " " + errorThrown);
			setStatus(3);
			CONFIG.status = 0;
			$('#load_modal').modal('hide');
			//设置定时重试
			setTimeout(function () {
				getCurrent();
				setStatus(2);
			}, 1000);
		}
	});
}

//设置服务器状态 1 已连接 2 重新连接 3 已断开
function setStatus(kind) {

	$('#status').removeClass('label-default label-success label-warning label-danger');


	switch (kind) {
	case 1:
		$('#status').addClass("label-success");
		$('#status').text('已连接服务器');
		break;
	case 2:
		$('#status').addClass("label-warning");
		$('#status').text('正在连接服务器');
		break;
	case 3:
		$('#status').addClass("label-danger");
		$('#status').text('已断开服务器');
		break;

	}

}

//绑定表格点击高亮及定位
function addClick2() {
	$('#table tbody').on('click', 'tr', function () {

		TABLE.$('tr.selected').removeClass('selected');
		$(this).addClass('selected');
		var row = TABLE.row($(this));
		locateItem(row.data().id);

	});



}

//点亮导航条位置
function initNavBar() {
	$('#nav_index_map').addClass('active');
}

/*初始化数据*/
function init() {

	initNavBar();

	//模态 窗口
	$('#load_modal').modal({
		show: true, //显示
		keyboard: false, //键盘ESC
		backdrop: 'static' //点击空白处不可关闭
	});

	CONFIG = new Object();
	CONFIG.init = 1; //默认第一次是初始化请求
	CONFIG.updatedTime = ''; //上次更新的最新时间
	CONFIG.status = 1; //0为意外断线

	itemlist = {}; //存储所有井盖的信息

	TABLE = $('#table').DataTable({
		"oLanguage": { // 汉化
			"sProcessing": "正在加载数据...",
			"sLengthMenu": "显示 _MENU_ 条 ",
			"sZeroRecords": "没有数据",
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
		"bPaginate": false, // 分页按钮
		"bFilter": false, // 搜索栏
		"bLengthChange": false, // 每行显示记录数
		"bAutoWidth": false, //一行显示
		"bScrollInfinite": true, //滚动条
		"bSort": true, // 排序
		"bInfo": false, //页脚信息
		"sPaginationType": "bootstrap",
		"columns": [
			{
				"data": "id"
			},
			{
				"data": "address"
			},
			{
				"data": "status"
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
	addClick2(); //添加点击响应

	//地图右上角工具栏
	var top_right_navigation = new BMap.NavigationControl({
		anchor: BMAP_ANCHOR_TOP_RIGHT,
		type: BMAP_NAVIGATION_CONTROL_SMALL
	}); //右上角，仅包含平移和缩放按钮

	/*百度地图	*/
	map = new BMap.Map("allmap", {
		enableMapClick: false /*设置不可点击覆盖物*/
	});
	map.centerAndZoom(new BMap.Point(116.417854, 39.921988), 15);
	map.enableScrollWheelZoom();
	/*开启滚轮缩放*/

	map.addControl(top_right_navigation); //右上角

	//点击切换显示列表
	$(".suojing").click(function () {
		$(".table-responsive").toggle();
	});

	//id, number, lat, lng, address, type, time, angle, volt, describe
	/*addItem(1, 13797741868, 116.417854, 39.921988, "北京市东城区王府井大街1", 1, "2015-06-14 15:14:26", 0, 3.58);
	addItem(2, 13797741867, 116.406605, 39.921585, "北京市东城区东华门大街1", 2, "2015-06-14 15:14:26", 4, 3.4);
	addItem(3, 13797741866, 116.412222, 39.912345, "北京市东城区正义路1", 1, "2015-06-14 15:14:26", 0, 3.58);*/

	//开始获取数据
	getCurrent();
	setStatus(2); //设置标签为正在连接
}