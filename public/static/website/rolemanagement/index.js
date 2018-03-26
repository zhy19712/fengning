//字符解码
function ajaxDataFilter(treeId, parentNode, responseData) {

    if (responseData) {
        for(var i =0; i < responseData.length; i++) {
            responseData[i] = JSON.parse(responseData[i]);
            responseData[i].name = decodeURIComponent(responseData[i].name);
        }
    }
    return responseData;
}
//组织结构树
var setting = {
    view: {
        showLine: true, //设置 zTree 是否显示节点之间的连线。
        selectedMulti: false, //设置是否允许同时选中多个节点。
        // dblClickExpand: true //双击节点时，是否自动展开父节点的标识。
    },
    async: {
        enable : true,
        autoParam: ["pid"],
        type : "post",
        url : "./roletree",
        dataType :"json",
        dataFilter: ajaxDataFilter
    },
    data:{
        keep: {
            leaf : true,
            parent : true
        },
        simpleData : {
            enable:true,
            idkey: "id",
            pIdKey: "pid",
            rootPId:0
        }
    },
    callback: {
        onClick: this.onClick

    }
};

zTreeObj = $.fn.zTree.init($("#ztree"), setting, null);

//组织结构表格
var tableItem = $('#tableItem').DataTable( {
    pagingType:   "full_numbers",
    processing: true,
    serverSide: true,
    ajax: {
        "url":"{:url('admin/common/datatablesPre?tableName=admin_cate&pid=')}"
    },
    dom: 'lf<"mybtn layui-btn layui-btn-sm">rtip',
    columns:[
        {
            name: "id"
        },
        {
            name: "role_name"
        },
        {
            name: "create_owner"
        },
        {
            name: "create_time"
        },
        {
            name: "desc"
        },
        {
            name: "desc"
        }
    ],
    columnDefs: [
        {
            "searchable": false,
            "orderable": false,
            "targets": [5],
            "render" :  function(data,type,row) {
                var html = "<i class=\"layui-icon\"></i>" ;
                html += "<i class=\"layui-icon\"></i>" ;
                html += "<i class=\"layui-icon\">&#xe640;</i>" ;
                return html;
            }
        }
    ],
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
$(".mybtn").html("新增");
//
layui.use('element', function(){
    var $ = layui.jquery
        ,element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块
});