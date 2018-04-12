$.ztree({
    //点击节点
    zTreeOnClick:function (event, treeId, treeNode){
        tableInfo();
        $.clicknode({
            tableItem:tableItem,
            treeNode:treeNode,
            tablePath:'/quality/common/datatablesPre?tableName=quality_unit',
            isLoadPath:false
        });
        $.ajax({
            url: "./unitPlanning",
            type: "post",
            data: {
                add_id:window.treeNode.add_id
            },
            dataType: "json",
            success: function (res) {
                if(res.code=1){

                }
            }
        });
    }
});

function tableInfo() {
    $.datatable({
        tableId:'tableItem',
        ajax:{
            'url':'/quality/common/datatablesPre?tableName=quality_unit'
        },
        dom: 'lf<".current-path"<"#add.add layui-btn layui-btn-normal layui-btn-sm">>tipr',
        columns:[
            {
                name: "serial_number"
            },
            {
                name: "site"
            },
            {
                name: "coding"
            },
            {
                name: "hinge"
            },
            {
                name: "pile_number"
            },
            {
                name: "start_date"
            },
            {
                name: "completion_date"
            },
            {
                name: "id"
            }
        ],
        columnDefs:[
            {
                "searchable": false,
                "orderable": false,
                "targets": [7],
                "render" :  function(data,type,row) {
                    var html = "<i class='fa fa-pencil' uid="+ data +" title='编辑' onclick='edit(this)'></i>" ;
                    html += "<i class='fa fa-trash' uid="+ data +" title='删除' onclick='del(this)'></i>" ;
                    html += "<i class='fa fa-qrcode' uid="+ data +" title='二维码' onclick='qrcode(this)'></i>" ;
                    return html;
                }
            }
        ],
    });
}
tableInfo();
