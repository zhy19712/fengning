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
        //serverSide: true,
        data:[
            [1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],[1,2,3,4,5,6,7],
        ],
        /*ajax: {
            "url": "__SCRIPT__/safety_edupeople.php"
        },
        dom: 'lf<"#manageExportExcel.export-excel btn-outline btn-primary"><"#manageExport.export btn-outline btn-primary"><"#manageImport.import btn-outline btn-primary"><"#manageAdd.add btn-outline btn-primary">rtip',*/
        columnDefs: [
            {
                "searchable": false,
                "orderable": false,
                "targets": [7],
                "render" :  function(data,type,row) {
                    var html = "<button type='button' class='' style='margin-left: 5px;' onclick='manageEdit(this)'><i class='fa fa-search'></i></button >" ;
                    html += "<button type='button' class='' style='margin-left: 5px;' onclick='manageDel(this)'><i class='fa fa-pencil'></i></button>" ;
                    html += "<button type='button' class='' style='margin-left: 5px;' onclick='manageDel(this)'><i class='fa fa-cog'></i></button>" ;
                    html += "<button type='button' class='' style='margin-left: 5px;' onclick='manageDel(this)'><i class='fa fa-user-secret'></i></button>" ;
                    return html;
                }
            }
        ],
        language: {
            "sProcessing":"数据加载中...",
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