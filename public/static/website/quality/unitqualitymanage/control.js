//初始化layui组件
var initUi = layui.use('form','laydate');
var form = layui.form;

$.ztree({
    //点击节点
    zTreeOnClick:function (event, treeId, treeNode){
        $('#enginId').val(treeNode.add_id);
        $.clicknode({
            tableItem:window.tableItem,
            treeNode:treeNode,
            isLoadPath:false,
            isLoadTable:false,
            parentShow:false
        });
        var iShow = treeNode.edit_id;
        var url = "../../productionProcesses";
        if(iShow){
            getControlPoint(url);
        }
    }
});


$.datatable({
    tableId:'tableItem',
    ajax:{
        'url':'/quality/common/datatablesPre?tableName=unit_quality_control'
    },
    dom: 'ltipr',
    columns:[
        {
            name: "id"
        },
        {
            name: "code"
        },
        {
            name: "name"
        },
        {
            name: "status"
        }
    ],
    columnDefs:[
        {
            "searchable": false,
            "orderable": false,
            "targets": [3],
            "render" :  function(data,type,row) {
                var html = "<i class='fa fa-download' uid="+ data +" title='下载' onclick='download(this)'></i>" ;
                html += "<i class='fa fa-print' uid="+ data +" title='打印' onclick='print(this)'></i>" ;
                return html;
            }
        }
    ],
});
