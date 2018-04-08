var selfid,zTreeObj,groupid,sNodes,selectData="";//选中的节点id，ztree对象，父节点id，选中的节点，选中的表格的信息
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
        url : "./picturetree",
        dataType :"json",
        dataFilter: ajaxDataFilter
    },
    data:{
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
//点击获取路径
function onClick(e, treeId, node) {
    selectData = "";
    $(".layout-panel-center .panel-title").text("");
    sNodes = zTreeObj.getSelectedNodes();//选中节点
    selfid = zTreeObj.getSelectedNodes()[0].id;
    var path = sNodes[0].name; //选中节点的名字
    node = sNodes[0].getParentNode();//获取父节点
    //判断是否还有父节点
    if (node) {
        //判断是否还有父节点
        while (node){
            path = node.name + "-" + path;
            node = node.getParentNode();
        }
    }else{
        $(".layout-panel-center .panel-title").text(sNodes[0].name);
    }
    groupid = sNodes[0].pId //父节点的id
    var url = "/archive/common/datatablespre/tableName/atlas_cate/selfid/"+selfid+".shtml";
    // tableItem.clear();
    tableItem.ajax.url(url).load();
    $(".layout-panel-center .panel-title").text("当前路径:"+path)
};
//获取点击行
$("#tableItem").delegate("tbody tr","click",function (e) {
    if($(e.target).hasClass("dataTables_empty")){
        return;
    }
    //点击展示图片
    if($(e.target).hasClass("showclick")){
        var data =  tableItem.row($(this)).data();//数据
        //判断显示隐藏
        if($(e.target).hasClass("fa-caret-down")){
            $(e.target).removeClass("fa-caret-down").addClass("fa-caret-right");
            $("#tableItem tbody tr.c"+data[13]).hide();
        }else{
            $(e.target).removeClass("fa-caret-right").addClass("fa-caret-down");
            $("#tableItem tbody tr.c"+data[13]).show();
        };
    }
    selectData="";
    drawingId='';
    $(".path").html("");
    $(this).addClass("select-color").siblings().removeClass("select-color");
    //判断是不是图册
    if($(this).hasClass("drawing")){
        isAtlas = false;
        drawingId = $(this).data("sid");
        $(".path").html($(".layout-panel-center .panel-title").html().split("-").pop()+"-"+$(this).find("td:nth-child(3)").html());
        //下载记录
        var url = "/archive/common/datatablespre/tableName/atlas_download_record/id/"+drawingId+".shtml";
        downlog.ajax.url(url).load();
        getAdminname(drawingId);
    }else{
        isAtlas = true;
        $(this).addClass("select-color").siblings().removeClass("select-color");
        selectData = tableItem.row(".select-color").data();//获取选中行数据
        $(".path").html($(".layout-panel-center .panel-title").html().split("-").pop()+"-"+selectData[2]);
        //下载记录
        var url = "/archive/common/datatablespre/tableName/atlas_download_record/id/"+selectData[13]+".shtml";
        downlog.ajax.url(url).load();
        getAdminname(selectData[13]);
    }
});
//点击编辑
function conEdit(id) {
    layer.open({
        type: 1,
        title: '图册管理—新增',
        area: ['690px', '540px'],
        content:atlasFormDom
    });
    $("#addId").val(selfid);
    $("#section").html(section);
    //日期

    layui.laydate.render({
        elem: '#completion_date',
        type: 'month'
    });
    layui.form.render();
    $.ajax({
        type:"post",
        url:"./getindex",
        data:{id:id},
        dataType:"json",
        success:function (res) {
            if(res.code===1){
                $("#picture_number").val(res.data.picture_number);
                $("#picture_name").val(res.data.picture_name);
                $("#picture_papaer_num").val(res.data.picture_papaer_num);
                $("#a1_picture").val(res.data.a1_picture);
                $("#design_name").val(res.data.design_name);
                $("#check_name").val(res.data.check_name);
                $("#examination_name").val(res.data.examination_name);
                $("#completion_date").val(res.data.completion_date);
                $("#section").val(res.data.section);
                $("#paper_category").val(res.data.paper_category);
                $("#editId").val(res.data.id);
                layui.form.render('select');
            }
        }
    })
}
//点击删除
function conDel(id){
    $.ajax({
        type:"post",
        url:"./delCateone",
        data:{id:id},
        dataType:"json",
        success:function (res) {
            if(res.code===1){
                layer.msg("删除成功");
                var url = "/archive/common/datatablespre/tableName/atlas_cate/selfid/"+selfid+".shtml";
                tableItem.ajax.url(url).load();
            }else if(res.code===-1){
                layer.msg(res.msg);
            }
        }
    })
}

//下载
function download(id,url) {
    var url1 = url;
    $.ajax({
        url: url,
        type:"post",
        dataType: "json",
        data:{id:id},
        success: function (res) {
            if(res.code != 1){
                layer.msg(res.msg);
            }else {
                $("#form_container").empty();
                var str = "";
                str += ""
                    + "<iframe name=downloadFrame"+ id +" style='display:none;'></iframe>"
                    + "<form name=download"+id +" action="+ url1 +" method='get' target=downloadFrame"+ id + ">"
                    + "<span class='file_name' style='color: #000;'>"+str+"</span>"
                    + "<input class='file_url' style='display: none;' name='id' value="+ id +">"
                    + "<button type='submit' class=btn" + id +"></button>"
                    + "</form>"
                $("#form_container").append(str);
                $("#form_container").find(".btn" + id).click();
                var url = "/archive/common/datatablespre/tableName/atlas_download_record/id/"+id+".shtml";
                setTimeout(function () {
                    downlog.ajax.url(url).load();
                },200);
            }

        }
    })
}
function conDown(id) {

    download(id,"./atlascateDownload")
}
//预览
function showPdf(id,url) {
    $.ajax({
        url: url,
        type: "post",
        data: {id:id},
        success: function (res) {
            console.log(res);
            if(res.code === 1){
                var path = res.path;
                console.log(res.path.split(".")[1]);
                if(res.path.split(".")[1]==="pdf"){
                    window.open("/static/public/web/viewer.html?file=../../../" + path,"_blank");
                }else{
                    // var index = layer.open({
                    //     type: 2,
                    //     title: '文件在线预览：' ,
                    //     shadeClose: true,
                    //     shade: 0.8,
                    //     area: ['980px', '600px'],
                    //     content: './pictureshow?url=' //iframe的url
                    // });
                    // layer.full(index);
                    layer.photos({
                        photos: {
                            "title": "", //相册标题
                            "id": id, //相册id
                            "start": 0, //初始显示的图片序号，默认0
                            "data": [   //相册包含的图片，数组格式
                                {
                                    "alt": "图片名",
                                    "pid": id, //图片id
                                    "src": "../../../"+res.path, //原图地址
                                    "thumb": "" //缩略图地址
                                }
                            ]
                        }
                        ,anim: Math.floor(Math.random()*7) //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
                    });
                }

            }else {
                layer.msg(res.msg);
            }
        }
    })
}
//预览图纸
function conPicshow(id){
    showPdf(id,'./atlascatePreview')

}