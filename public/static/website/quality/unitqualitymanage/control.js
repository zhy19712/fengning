//初始化layui组件
var initUi = layui.use('form','laydate');
var form = layui.form;

$.ztree({
    treeId:'controlZtree',
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

function unitPlanList() {
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
}


//控制点执行情况
function implementMethod() {
    $.datatable({
        tableId:'implement',
        ajax:{
            'url':'/quality/common/datatablesPre?tableName=unit_quality_manage_file'
        },
        dom: 'ltipr',
        columns:[
            {
                name: "filename"
            },
            {
                name: "nickname"
            },
            {
                name: "role_name"
            },
            {
                name: "create_time"
            },
            {
                name: "id"
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
}

//图像资料
function imageDataMethod() {
    $.datatable({
        tableId:'implement',
        ajax:{
            'url':'/quality/common/datatablesPre?tableName=unit_quality_manage_file'
        },
        dom: 'ltipr',
        columns:[
            {
                name: "filename"
            },
            {
                name: "nickname"
            },
            {
                name: "role_name"
            },
            {
                name: "create_time"
            },
            {
                name: "id"
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
}


$('.tabs').after('<button class="layui-btn">上传</button>');

