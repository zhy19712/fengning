var selfid,zTreeObj,groupid,sNodes,selectData="";//选中的节点id，ztree对象，父节点id，选中的节点，选中的表格的信息
var uploadpath;
var year="",month="",day="";
//编辑dom
var sceneDom =[
    '<form  id="sceneform" action="#" onsubmit="return false" class="layui-form" style="padding-top: 20px;">',
    '    <input type="hidden" name="id" id="addId" style="display: none;">',
    '    <div class="layui-form-item">',
    '            <div class="autograph">',
    '               <label class="layui-form-label">名称</label>',
    '               <div class="layui-input-inline">',
    '                   <input type="text" name="filename" id="filename" lay-verify="required"  autocomplete="off" class="layui-input">',
    '               </div>',
    '           </div>',
    '    </div>',
    '    <hr class="layui-bg-gray">',
    '    <div class="layui-form-item">',
    '       <div class="col-xs-12" style="text-align: center;">',
    '           <button class="layui-btn" lay-submit="" lay-filter="demo1"><i class="fa fa-save"></i> 保存</button>&nbsp;&nbsp;&nbsp;',
    '           <button type="reset" class="layui-btn layui-btn-danger layui-layer-close"><i class="fa fa-close"></i> 返回</button>',
    '       </div>',
    '    </div>',
    '</form>'].join("");
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
        url : "./tree",
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

layui.use(['element',"layer",'form','laydate','upload'], function(){
    var $ = layui.jquery
        ,element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块

    var form = layui.form
        ,layer = layui.layer
        ,layedit = layui.layedit
        ,upload = layui.upload
        ,laydate = layui.laydate;


    //监听提交
    form.on('submit(demo1)', function(data){
        $.ajax({
            type: "post",
            url:"./edit",
            data:data.field,
            success: function (res) {
                if(res.code == 1) {
                    var url = "/quality/common/datatablespre/tableName/"+tableName+"/year/"+year+"/month/"+month+"/day/"+day+".shtml";
                    tableItem.ajax.url(url).load();
                    parent.layer.msg('保存成功！');
                    layer.closeAll();
                }
            },
            error: function (data) {
                debugger;
            }
        });
        return false;
    });
    //上传图片
    upload.render({
        elem: '#upload',
        url: '../../quality/common/qualityUpload?module=quality&use=quality_thumb',
        accept: 'file',//普通文件
        before: function(obj){
            obj.preview(function(index, file, result){
            })
        },
        done:function (res) {
            $.ajax({
                type:"post",
                data:{path:res.src},
                url:"./add",
                dataType:"json",
                success:function (res) {
                    if(res.code===1){
                        layer.msg("上传成功");
                        var url = "/quality/common/datatablespre/tableName/"+tableName+"/year/"+year+"/month/"+month+"/day/"+day+".shtml";
                        tableItem.ajax.url(url).load();
                        zTreeObj.reAsyncChildNodes(null, "refresh", false);
                        setTimeout(function () {
                            zTreeObj.expandAll(true);
                        },700);

                    }
                }
            })
        }
    });
});

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
    groupid = sNodes[0].pId; //父节点的id
    //获取年月日
    year = path.split("-")[1]?path.split("-")[1].substr(0,path.split("-")[1].length-1):"";
    month = path.split("-")[2]?path.split("-")[2].substr(0,path.split("-")[2].length-1):"";
    day = path.split("-")[3]?path.split("-")[3].substr(0,path.split("-")[3].length-1):"";
    var url = "/quality/common/datatablespre/tableName/"+tableName+"/year/"+year+"/month/"+month+"/day/"+day+".shtml";
    tableItem.ajax.url(url).load();
    $(".layout-panel-center .panel-title").text("当前路径:"+path)
};
//获取点击行
$("#tableItem").delegate("tbody tr","click",function (e) {
    if($(e.target).hasClass("dataTables_empty")){
        return;
    }
    selectData="";
    $(".path").html("");
    $(this).addClass("select-color").siblings().removeClass("select-color");
    $(this).addClass("select-color").siblings().removeClass("select-color");
    selectData = tableItem.row(".select-color").data();//获取选中行数据
    $(".path").html($(".layout-panel-center .panel-title").html().split("-").pop()+"-"+selectData[2]);
});
//点击编辑
function conEdit(id) {
    layer.open({
        type: 1,
        title: '编辑',
        area: ['690px', '240px'],
        content:sceneDom
    });
    $("#addId").val(id);

    $.ajax({
        type:"post",
        url:"./getindex",
        data:{id:id},
        dataType:"json",
        success:function (res) {
            if(res.code===1){
                $("#filename").val(res.data.filename);
            }
        }
    })
}
//点击删除
function conDel(id){
    $.ajax({
        type:"post",
        url:"./del",
        data:{id:id},
        dataType:"json",
        success:function (res) {
            if(res.code===1){
                layer.msg("删除成功");
                var url = "/quality/common/datatablespre/tableName/"+tableName+"/year/"+year+"/month/"+month+"/day/"+day+".shtml";
                tableItem.ajax.url(url).load();
                zTreeObj.reAsyncChildNodes(null, "refresh", false);
                setTimeout(function () {
                    zTreeObj.expandAll(true);
                },700);
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
            }

        }
    })
}
function conDown(id) {

    download(id,"./download")
}
//预览
function showPdf(id,url) {
    $.ajax({
        url: url,
        type: "post",
        data: {id:id},
        success: function (res) {
            if(res.code === 1){
                var path = res.path;
                if(res.path.split(".")[1]==="pdf"){
                    window.open("/static/public/web/viewer.html?file=../../../" + path,"_blank");
                }else if(res.path.split(".")[1]==="png"||res.path.split(".")[1]==="jpg"||res.path.split(".")[1]==="jpeg"){
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
                }else{
                    layer.msg("不支持的文件格式");
                }

            }else {
                layer.msg(res.msg);
            }
        }
    })
}
//预览
function conPicshow(id){
    showPdf(id,'./preview')

}
//设置位置
function conPosition(id) {
    layer.open({
        type: 2,
        shadeClose: true,
        title: "空间位置设置",
        area: ["90%", "90%"],
        content: "../scenepicture/PositionSet?id=" + id
    });
}