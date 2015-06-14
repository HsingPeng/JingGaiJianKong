/*添加地图上的标记*/
function addMarker(id, lat, lon, info, type) {
	var color = 1;
	var status = "井盖正常";
	if (type === 2) {
		color = -46;
		status = "井盖报警";
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

	var point = new BMap.Point(lat, lon);
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
function addItem(id, number, lat, lon, address, type, time, angle, volt, describe) {
	itemlist[id] = {
		id: id,
		number: number,
		lat: lat,
		lon: lon,
		address: address,
		type: type,
		time: time,
		angle: angle,
		volt: volt,
		describe: describe,
		infowindow: null
	};
	addMarker(id, lat, lon, address, type);
	addTable2(id, address, type, angle, volt, time);

}

/*添加列表数据*/
function addTable(id, address, type, angle, volt, time) {

	var status = type == 1 ? "正常" : "倾斜报警";
	var color = type == 1 ? "normal" : "danger";
	var txt1 = "<tr class=\"" + color + '\"' + 'id=i_' + id + '>';
	txt1 += '<th scope=\"row\">' + id + "</th>";
	txt1 += "<td>" + address + "</td>";
	txt1 += "<td>" + status + "</td>";
	txt1 += "<td>" + angle + "</td>";
	txt1 += "<td>" + volt + "</td>";
	txt1 += "<td>" + time + "</td>";

	var tr_t = $("#i_" + id + ":first"); //判断是否已经在表格上

	if (tr_t.length) { //在，替换
		tr_t.after(txt1);
		tr_t.remove();
	} else {
		$("tbody").append(txt1);
	}

	addClick(id);

}

function addTable2(id, address, type, angle, volt, time) {

	var status = type == 1 ? "正常" : "倾斜报警";
	var color = type == 1 ? "normal" : "danger";
	TABLE.row.add({
		id: id,
		address: address,
		status: status,
		angle: status,
		volt: status,
		time: time
	}).draw();

}

function addClick(id) {

	$("#i_" + id).click(function () {
		locateItem(id);
	});
}

/*定位到中心*/
function locateItem(id) {
	try {
		var lat_t = itemlist[id]["lat"];
		var lon_t = itemlist[id]["lon"];
		var point = new BMap.Point(lat_t, lon_t);
		map.panTo(point);
		map.openInfoWindow(itemlist[id]["infowindow"], point); /*开启信息窗口*/
	} catch (e) {
		console.warn('locateItem' + ' id ' + id + '' + e.name + ' ' + e.message);
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
				var data = msg.data;
				for (var y in data) {

					var x = data[y];
					addItem(x["id"] * 1, x["number"] * 1, x["lat"] * 1, x["lon"] * 1, x["address"], x["type"] * 1, x["time"], x["angle"] * 1, x["volt"] * 1, x["describe"]);

				}

				//把上次更新的项放进去
				if (CONFIG.init) {
					//地图中心定位到第一个item

					var tr = $("tbody tr:first");
					
					var row = TABLE.row(tr);;
					var id = row.data().id ;
					if (id != null) {
						locateItem(id);
					}

					CONFIG.init = 0; //关闭初始化请求

				}

				CONFIG.updatedTime = msg.updatedTime;


			} catch (e) {
				alert("获取数据错误: " + e.name + ' ' + e.message);
			}

			//getCurrent();
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
				//CONFIG.status = 1;
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

//绑定表格点击高亮
function addClick2() {
	$('#table tbody').on('click', 'tr', function () {
		if ($(this).hasClass('selected')) {
			$(this).removeClass('selected');
		} else {
			TABLE.$('tr.selected').removeClass('selected');
			$(this).addClass('selected');
			var row = TABLE.row($(this));
			locateItem(row.data().id);
		}
	});



}

/*初始化数据*/
function init() {

	//模态 窗口
	/*$('#load_modal').modal({
		show: true,
		keyboard: false,
		backdrop: 'static'
	});*/

	CONFIG = new Object();
	CONFIG.init = 1; //默认第一次是初始化请求
	CONFIG.updatedTime = ''; //上次更新的最新时间
	CONFIG.status = 1;

	itemlist = {}; //存储所有井盖的信息

	TABLE = $('#table').DataTable({
		"oLanguage": { // 汉化
			"sProcessing": "正在加载数据...",
			"sLengthMenu": "显示 _MENU_ 条 ",
			"sZeroRecords": "没有您要搜索的内容",
			/*"sInfo": "从_START_ 到 _END_ 条记录——总记录数为 _TOTAL_ 条",*/
			"sInfo": "",
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
		"bScrollInfinite": true,
		"bSort": true, // 排序
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
        ]
	});
	addClick2();

	var top_right_navigation = new BMap.NavigationControl({
		anchor: BMAP_ANCHOR_TOP_RIGHT,
		type: BMAP_NAVIGATION_CONTROL_SMALL
	}); //右上角，仅包含平移和缩放按钮

	/*百度地图	*/
	map = new BMap.Map("allmap", {
		enableMapClick: false /*设置不可点击覆盖物*/
	});
	map.centerAndZoom(new BMap.Point(116.417854, 39.921988), 15);
	/*map.enableScrollWheelZoom(); */
	/*开启滚轮缩放*/

	map.addControl(top_right_navigation); //右上角

	$(".suojing").click(function () {
		$(".table-responsive").toggle();
	});

	/*$("#manage").click(function(){
		
		location.href=('home.php?m=Home&c=Index&a=manage') ;
	});
	
	$("#logout").click(function(){
		location.href=('home.php?m=Home&c=Index&a=login') ;
	});*/

	//id, number, lat, lon, address, type, time, angle, volt, describe
	addItem(1, 13797741868, 116.417854, 39.921988, "北京市东城区王府井大街", 1, "2015年6月8日16点51分11秒", 0, 3.58);
	addItem(2, 13797741867, 116.406605, 39.921585, "北京市东城区东华门大街", 2, "2015年6月8日16点51分11秒", 4, 3.4);
	addItem(3, 13797741866, 116.412222, 39.912345, "北京市东城区正义路", 1, "2015年6月8日16点51分11秒", 0, 3.58);

	/*TABLE.row.add([2, "北京市东城区王府井大街", "正常", 0, 4, "2015-06-11 01:28:51"]).draw();
	 */
	//开始获取数据
	getCurrent();
	setStatus(2); //设置标签为正在连接
}