//初始化layui组件
var initUi = layui.use('form');
//工程标准及规范树
$.ztree();
//点击节点
function zTreeOnClick(event, treeId, treeNode){
    $.clicknode({
        treeNode:treeNode,
        tablePath:'/standard/common/datatablesPre?tableName=norm_file',
        isLoadPath:false
    });
}
//工程标准及规范表格
$.datatable({
    ajax:{
        'url':'/standard/common/datatablesPre?tableName=norm_file'
    },
    columns:[
        {
            name: "standard_number"
        },
        {
            name: "standard_name"
        },
        {
            name: "material_date"
        },
        {
            name: "alternate_standard"
        },
        {
            name: "remark"
        },
        {
            name: "id"
        }
    ],
    columnDefs:[
        {
            "searchable": false,
            "orderable": false,
            "targets": [5],
            "render" :  function(data,type,row) {
                var html = "<i class='fa fa-search' uid="+ data +" title='下载' onclick='download(this)'></i>" ;
                return html;
            }
        }
    ],
});
//新增弹层
$.add({
    area:['660px','350px']
});
//关闭弹层
$.close();
//表单提交
$.submit({
    ajaxUrl:'./editNode',
    tablePath:'/standard/common/datatablesPre?tableName=norm_file'
});
