/*初始化数据*/
function init() {

	initNavBar();
	
	TABLE = initTable();
	MODAL = 0;
	addClick();

	initMap();

	addButton();

}

//点亮导航条位置
function initNavBar(){
	$('#nav_manage').addClass('active');
	$('#nav_manage_jg').addClass('active');
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
		
		$('#input-id').val('自动生成');
		$("#input-id").attr("disabled", true);
		$('#safetime').val('');
		$('#number').val('');
		$('#uid').val(uid);
		if(uid!=1){
			$("#uid").attr("disabled", true);
		}
		$('#lng').text('点击地图自动获取');
		$('#lat').text('点击地图自动获取');
		$('#address').val('');
		$('#describe').val('');

		$('#myModalLabel').text('添加设备');
		//打开模态窗口
		$('#myModal').modal({
			show: true, //显示
			keyboard: false, //键盘ESC
			backdrop: 'static' //点击空白处不可关闭
		});

		map.clearOverlays();

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
			url: 'home.php?m=Home&c=ManageJG&a=create',
			data: {
				safetime: $('#safetime').val(),
				number: $('#number').val(),
				uid: $('#uid').val(),
				lng: $('#lng').html(),
				lat: $('#lat').html(),
				address: $('#address').val(),
				describe: $('#describe').val(),
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
					alert("添加成功！设备ID为："+msg.id);
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

		$.ajax({
			url: 'home.php?m=Home&c=ManageJG&a=update',
			data: {
				id: $('#input-id').val(),
				number: $('#number').val(),
				uid: $('#uid').val(),
				lng: $('#lng').html(),
				lat: $('#lat').html(),
				safetime:$('#safetime').val(),
				address: $('#address').val(),
				describe: $('#describe').val(),
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

function checkInput() {

	var flag = true;
	if ($('#safetime').val() == '') {
		//alert("设备ID未填写");
		$('#safetime').focus();
		flag = false;
	} else if ($('#number').val() == '') {
		//alert("手机号未填写");
		$('#number').focus();
		flag = false;
	}
	else if ($('#uid').val() == '') {
		//alert("uid未填写");
		$('#uid').focus();
		flag = false;
	}else if ($('#address').val() == '') {
		$('#address').focus();
		flag = false;
	} else if ($('#lng').html() == '点击地图自动获取') {
		alert("请点击地图选择位置");
		flag = false;
	}

	return flag;
}

function initMap() {

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
	/*map.enableScrollWheelZoom();*/
	/*开启滚轮缩放*/

	map.addControl(top_right_navigation); //右上角

	var geoc = new BMap.Geocoder();

	map.addEventListener("click", function (e) {
		var pt = e.point;
		map.clearOverlays();
		map.addOverlay(new BMap.Marker(pt));
		geoc.getLocation(pt, function (rs) {
			var addComp = rs.addressComponents;
			$('#lng').text(pt.lng);
			$('#lat').text(pt.lat);
			var streetNumber = (addComp.streetNumber == '') ? '' : (',' + addComp.streetNumber);
			$('#address').val(addComp.province + ", " + addComp.city + ", " + addComp.district + ", " + addComp.street + streetNumber);

			//alert(pt.lng + " " + pt.lat + addComp.province + ", " + addComp.city + ", " + addComp.district + ", " + addComp.street + ", " + addComp.streetNumber);
		});
	});
}

/**
 * 表格初始化
 * @returns {*|jQuery}
 */
function initTable() {
	var table = $("#table").DataTable({
		
		"sAjaxSource": 'home.php?m=Home&c=ManageJG&a=retrieve',
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
		"iDisplayLength" : 10,// 每页显示行数
		"bAutoWidth": false, //一行显示
		"columns": [
			{
				"data": "id"
			},
			{
				"data": "number"
			},
			{
				"data": "lng"
			},
			{
				"data": "lat"
			},
			{
				"data": "address"
			},
			{
				"data": "safetime"
			},
			{
				"data": "describe"
			},
			{
				"data": "uid"
			}
        ],
		"columnDefs": [{
			"render": function (data, type, row) {
				return '<a href="#" class="btn btn-primary" id="editFun">修改</a> ' + '&nbsp;' + '<a href="#" class="btn btn-danger" id="deleteFun">删除</a>' + '&nbsp;';
			},
			"targets": 8
      }],
	});
	return table;
}

function addClick() {
	$('#table tbody').on('click', '#editFun', function () {

		MODAL = 2;

		var row = TABLE.row($(this).closest('tr'));
		//alert(row.data().id);

		$('#input-id').val(row.data().id);
		$("#input-id").attr("disabled", true);
		$('#safetime').val(row.data().safetime);
		$('#number').val(row.data().number);
		$('#uid').val(row.data().uid);
		if($("#USER_ID").attr("name")!=1){
			$("#uid").attr("disabled", true);
		}
		$('#lng').text(row.data().lng);
		$('#lat').text(row.data().lat);
		$('#address').val(row.data().address);
		$('#describe').val(row.data().describe);

		$('#myModalLabel').text('修改设备');
		//打开模态窗口
		$('#myModal').modal({
			show: true, //显示
			keyboard: false, //键盘ESC
			backdrop: 'static' //点击空白处不可关闭
		});

		map.clearOverlays();
		var point = new BMap.Point(row.data().lng, row.data().lat);
		map.addOverlay(new BMap.Marker(point));
		setTimeout(function () {
			map.panTo(point);
		}, 500);


	});

	$('#table tbody').on('click', '#deleteFun', function () {
		var row = TABLE.row($(this).closest('tr'));
		var r = confirm("确认删除ID为" + row.data().id + '的设备！');
		if (r == true) {
			sendDelete(row.data().id);
		}

	});
}


function sendDelete(id) {

	$('#load_modal').modal({
		show: true, //显示
		keyboard: false, //键盘ESC
		backdrop: 'static' //点击空白处不可关闭
	});

	$.ajax({
		url: 'home.php?m=Home&c=ManageJG&a=delete',
		data: {
			id: id,
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
				TABLE.ajax.reload();		//表格重新加载数据
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
