;(function($){
    /**
     * @param options
     * @author wyang
     */
    $.datatable = function(options){
        var option = {
            ajax: {
                "url":"/admin/common/datatablesPre?tableName=admin"
            }
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
            columns: options.columns,
            columnDefs: options.columnDefs,
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