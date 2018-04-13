//组织结构树
var setting = {
    view: {
        showLine: true, //设置 zTree 是否显示节点之间的连线。
        selectedMulti: false, //设置是否允许同时选中多个节点。
        // dblClickExpand: true //双击节点时，是否自动展开父节点的标识。
    },
    async: {
        enable : true,
        autoParam: ["pid","id"],
        type : "post",
        url : "../division/index",
        dataType :"json"
    },
    data:{
        simpleData : {
            enable:true,
            idkey: "id",
            pIdKey: "pId",
            rootPId:0
        }
    },
    callback: {
        onClick: this.onClick
    }
};
//初始化树
zTreeObj = $.fn.zTree.init($("#ztree"), setting, null);
//点击获取路径
function onClick(e, treeId, node) {
    selectData = "";
    sNodes = zTreeObj.getSelectedNodes();//选中节点
    selfid = zTreeObj.getSelectedNodes()[0].id;
    var path = sNodes[0].name; //选中节点的名字
    node = sNodes[0].getParentNode();//获取父节点
    //判断是否还有子节点
    if (!sNodes[0].children) {
        //判断是否还有父节点
        selfidName()
        $("#tableContent .imgList").css('display','block');
    }
    groupid = sNodes[0].pId //父节点的id
    // var url = "/archive/common/datatablespre/tableName/archive_atlas_cate/selfid/"+selfid+".shtml";
    // tableItem.ajax.url(url).load();
    $(".mybtn").css("display","none");//新增
    $(".alldel").css("display","none");//全部删除

    $("#homeWork").css("color","#2213e9");
}
//点击置灰
$(".imgList").on("click","a",function () {
    $(this).css("color","#2213e9").siblings("a").css("color","#CDCDCD");
    $("#homeWork").css("color","#CDCDCD");
});

//点击作业
$(".imgList").on("click","#homeWork",function () {
    $(".bitCodes").css("display","block");
    $(".mybtn").css("display","none");
    $(".alldel").css("display","none");
    $(this).css("color","#2213e9").parent("span").next("span").children("a").css("color","#CDCDCD");
    tableItem.ajax.url("{:url('/quality/common/datatablesPre')}?tableName=quality_division_controlpoint_relation&division_id="+selfidUnit).load();
});
//点击工序控制点名字
function clickConName(id) {
    conThisId = id;
    $(".bitCodes").css("display","bitCodes");
    $(".mybtn").css("display","block");
    $(".alldel").css("display","block");
    $("#tableContent .imgList").css('display','block');
    tableItem.ajax.url("{:url('/quality/common/datatablesPre')}?tableName=quality_division_controlpoint_relation&division_id="+selfidUnit+"&ma_division_id="+conThisId).load();
}
//初始化表格
var tableItem = $('#tableItem').DataTable( {
    pagingType: "full_numbers",
    processing: true,
    serverSide: true,
    // scrollY: 600,
    ajax: {
        "url":"../common/datatablesPre?tableName=quality_scene_picture"
    },
    dom: 'f<"alldel layui-btn layui-btn-sm"><"mybtn layui-btn layui-btn-sm"><"bitCodes layui-btn layui-btn-sm">rtlip',
    columns:[
        {
            name: "filename"
        },
        {
            name: "date"
        },
        {
            name: "owner"
        },
        {
            name: "company"
        },
        {
            name: "position"
        },
        {
            name: "id"
        }
    ],
    columnDefs: [
        {
            "searchable": false,
            "orderable": false,
            "targets": [5],
            "render" :  function(data,type,row) {
                var a = data;
                var html =  "<a type='button' href='javasrcipt:;' class='' style='margin-left: 5px;' onclick='conDown("+data+")'><i class='fa fa-download'></i></a>" ;
                html += "<a type='button' class='' style='margin-left: 5px;' onclick='conDown("+data+")'><i class='fa fa-print'></i></a>" ;
                html += "<a type='button' class='' style='margin-left: 5px;' onclick='conDel("+data+")'><i class='fa fa-trash'></i></a>" ;
                return html;
            }
        },
        {
            "orderable": false,
            "targets": [4],
            "render":function (data) {
                if(data==1){
                    return "<img src='__WEBSITE__/quality/scenepicture/setValid.png'>" ;
                }else{
                    return "";
                }
            }
        }
    ],
    language: {
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
    "fnInitComplete": function (oSettings, json) {
        $('#tableItem_length').insertBefore(".mark");
        $('#tableItem_info').insertBefore(".mark");
        $('#tableItem_paginate').insertBefore(".mark");
        $('.dataTables_wrapper,.tbcontainer').css("display","block");
    }
});
//
$(".bitCodes").html("<div id='bitCodes'><i class='fa fa-download' style='padding-right: 3px;'></i>导出二维码</div>");
$(".mybtn").html("<div id='test3'><i class='fa fa-plus'></i>新增控制点</div>");
$(".alldel").html("<div id='delAll'><i class='fa fa-close'></i>全部删除</div>");

//点击新增控制节点
$("#tableContent").on("click",".mybtn #test3",function () {
    console.log(conThisId);
    layer.open({
        type: 2,
        title: '控制点选择',
        shadeClose: true,
        area: ['980px', '673px'],
        content: '{:url("addplan")}?Division='+ selfidUnit + '&TrackingDivision='+ conThisId,
        end:function () {
            tableItem.ajax.url("{:url('/quality/common/datatablesPre')}?tableName=quality_division_controlpoint_relation&division_id="+selfidUnit+"&ma_division_id="+conThisId).load();
        }
    });
});

//删除控制点
function delFile(id) {
    console.log(id);
    $.ajax({
        type: "post",
        url: "{:url('quality/element/delControlPointRelation')}",
        data: {id:id},
        success: function (res) {
            console.log(res);
            if(res.code ==1){
                layer.msg("删除成功！")
                tableItem.ajax.url("{:url('/quality/common/datatablesPre')}?tableName=quality_division_controlpoint_relation&division_id="+selfidUnit+"&ma_division_id="+conThisId).load();
            }
        }
    })
}

