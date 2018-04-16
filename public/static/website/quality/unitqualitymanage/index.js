//初始化layui组件
var initUi = layui.use('form','laydate');
var form = layui.form;

$.datatable({
    tableId:'tableItem',
    ajax:{
        'url':'/quality/common/datatablesPre?tableName=unit_quality_control'
    },
    dom: 'ltpr',
    columns:[
        {
            name: "code"
        },{
            name: "name"
        },
        {
            name: "id"
        }
    ],
    columnDefs:[
        {
            "searchable": false,
            "orderable": false,
            "targets": [2],
            "render" :  function(data,type,row) {
                var html = "<i class='fa fa-download' uid="+ data +" title='下载' onclick='download(this)'></i>" ;
                html += "<i class='fa fa-print' uid="+ data +" title='打印' onclick='print(this)'></i>" ;
                html += "<i class='fa fa-times' uid="+ data +" title='删除' onclick='del(this)'></i>" ;
                return html;
            }
        }
    ],
});

$.datatable({
    tableId:'controlItem',
    ajax:{
        'url':'/quality/common/datatablesPre?tableName=unit_quality_add_control'
    },
    dom: 'ltpr',
    columns:[
        {
            name: "id",
            "render": function(data, type, full, meta) {
                var ipt = "<input type='checkbox' name='checkList' idv='"+data+"' onclick='getSelectId(this)'>";
                return ipt;
            },
        },
        {
            name: "code"
        },{
            name: "name"
        }
    ],
});
//取消全选的事件绑定
$("thead tr th:first-child").unbind();

//删除自构建分页位置
$('#easyuiLayout').find('.tbcontainer').remove();
/**
 * 导出二维码
 */
//事件
$('#exportQcodeBtn').click(function () {
    var addId = window.treeNode.add_id;
    exportQcode(addId);
});
//方法
function exportQcode(addId) {
    $.ajax({
        url: "./exportCode",
        type: "post",
        data: {
            add_id:addId
        },
        dataType: "json",
        success: function (res) {

        }
    })
}

/**
 * 添加控制点
 */
//事件
$('#addBtn').click(function () {
    addControl();
})

//方法
function addControl() {
    layer.open({
        title:'控制点选择',
        id:'1',
        type:'1',
        area:['1024px','500px'],
        content:$('#pointLayer'),
        success:function () {
            $('#pointLayer').css('visibility','initial');
        },
        cancel: function(index, layero){
            $('#pointLayer').css('visibility','hidden');
            layer.close(layer.index);
        }
    });
}

$.ztree({
    treeId:'controlZtree',
    ajaxUrl:'./unitTree',
    zTreeOnClick:function (event, treeId, treeNode){
        $('#controlItem_wrapper,.tbcontainer,#subList').show();
        $.clicknode({
            tableItem:tableItem,
            treeNode:treeNode,
            isLoadTable:true,
            isLoadPath:false,
            parentShow:false,
            tablePath:'/quality/common/datatablesPre?tableName=unit_quality_add_control'
        });
    }
});