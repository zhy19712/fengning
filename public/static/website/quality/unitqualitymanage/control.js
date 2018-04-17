//初始化layui组件
var initUi = layui.use('form','laydate');
var form = layui.form;

$.datatable({
    tableId:'tableItem',
    ajax:{
        'url':'/quality/common/datatablesPre?tableName=quality_division_controlpoint_relation'
    },
    dom: 'lftipr',
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
            "targets": [3],
            "render" :  function(data,type,row) {
                var html = "<i class='fa fa-download' uid="+ data +" title='下载' onclick='download(this)'></i>" ;
                html += "<i class='fa fa-print' uid="+ data +" title='打印' onclick='print(this)'></i>" ;
                return html;
            }
        }
    ],
});
