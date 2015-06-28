//初始化
function init() {
	TABLE = initTable();
	MODAL = 0;
	addButton();
	addClick();
}

function addClick() {
	
}

function addButton() {
	
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