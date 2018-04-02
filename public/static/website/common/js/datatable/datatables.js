;(function($){
    /**
     * @param options
     * @author wyang
     */
    $.datatable = function(options){
        var option = {
            ajax: {
                "url":"/admin/common/datatablesPre?tableName=admin"
            },
            columns:[
                {
                    name: "g_order"
                },
                {
                    name: "g_name"
                },
                {
                    name: "nickname"
                },
                {
                    name: "name"
                },
                {
                    name: "mobile"
                },
                {
                    name: "position"
                },
                {
                    name: "status"
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
                        var status = row[6];    //后台返回的是否禁用用户状态
                        var html = "<i class='fa fa-search' uid="+ data +" title='查看' onclick='view(this)'></i>" ;
                        html += "<i class='fa fa-pencil' uid="+ data +" title='编辑' onclick='editor(this)'></i>" ;
                        html += "<i class='fa fa-cog' uid="+ data +" title='重置密码' onclick='reset(this)'></i>" ;
                        if(status==1){
                            html += "<i class='fa fa-user-secret' uid="+ data +" status="+ status +" title='置为无效' onclick='audit(this)'></i>" ;
                        }else if(status==0){
                            html += "<i class='fa fa-user-times' uid="+ data +" status="+ status +" title='置为有效' onclick='audit(this)'></i>" ;
                        }
                        return html;
                    }
                }
            ]
        }

        $.extend(option,options);

        var  tbcontainer = '<div class="tbcontainer">' +
                            '<div class="mark"></div>' +
                            '</div>';
        $('table.table').after(tbcontainer);

        window.tableItem = $('#tableItem').DataTable( {
            pagingType: "full_numbers",
            processing: true,
            serverSide: true,
            ajax: option.ajax,
            columns: option.columns,
            columnDefs: option.columnDefs,
            language: {
                "sProcessing":"数据加载中...",
                "lengthMenu": "_MENU_",
                "zeroRecords": "没有找到记录",
                "info": "第 _PAGE_ 页 ( 共 _PAGES_ 页, _TOTAL_ 项 )",
                "infoEmpty": "无记录",
                "search": "搜索：",
                "infoFiltered": "(从 _MAX_ 条记录过滤)",
                "paginate": {
                    "sFirst": "<<",
                    "sPrevious": "<",
                    "sNext": ">",
                    "sLast": ">>"
                }
            },
            fnInitComplete: function (oSettings, json) {
                $('#tableItem_length').insertBefore(".mark");
                $('#tableItem_info').insertBefore(".mark");
                $('#tableItem_paginate').insertBefore(".mark");
            }
        });
    }
})(jQuery);