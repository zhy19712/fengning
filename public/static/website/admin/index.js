$(function(){
    //组织结构树
    var setting = {
        showLine:true,
        showTitle:true,
        showIcon:true
    }

    var nodes = [
        {name: "父节点1", children: [
                {name: "子节点1"},
                {name: "子节点2"}
            ]
        }
    ];

    zTreeObj = $.fn.zTree.init($("#ztree"), setting, nodes);

    //组织结构表格

    var tableItem = $('#tableItem').DataTable( {
        processing: true,
        serverSide: true,
        data:[
            [
                "Tiger Nixon",
                "System Architect",
                "Edinburgh",
                "5421",
                "2011/04/25",
                "2011/04/25",
                "2011/04/25",
                "$3,120"
            ],
            [
                "Garrett Winters",
                "Director",
                "Edinburgh",
                "8422",
                "2011/07/25",
                "2011/07/25",
                "2011/07/25",
                "$5,300"
            ]
        ],
        /*/!*ajax: {
            "url": "__SCRIPT__/safety_edupeople.php"
        },*!/
        dom: 'lf<"#manageExportExcel.export-excel btn-outline btn-primary"><"#manageExport.export btn-outline btn-primary"><"#manageImport.import btn-outline btn-primary"><"#manageAdd.add btn-outline btn-primary">rtip',
        columnDefs: [
            {
                "searchable": false,
                "orderable": false,
                "targets": [8],
                "render" :  function(data,type,row) {
                    var html = "<input type='button' class='btn btn-primary btn-outline btn-xs' style='margin-left: 5px;' onclick='manageEdit(this)' value='编辑'/>" ;
                    html += "<input type='button' class='btn btn-danger btn-outline btn-xs' style='margin-left: 5px;' onclick='manageDel(this)' value='删除'/>" ;
                    return html;
                }
            }
        ],*/
        language: {
            "lengthMenu": "每页_MENU_ 条记录",
            "zeroRecords": "没有找到记录",
            "info": "第 _PAGE_ 页 ( 总共 _PAGES_ 页 )",
            "infoEmpty": "无记录",
            "search": "搜索：",
            "infoFiltered": "(从 _MAX_ 条记录过滤)",
            "paginate": {
                "previous": "上一页",
                "next": "下一页"
            }
        }
    });
})