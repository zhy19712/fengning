var selfid,zTreeObj,groupid,sNodes,selectData="";//选中的节点id，ztree对象，父节点id，选中的节点，选中的表格的信息
var isAtlas;//是否是图册
var section='<option value=""></option>'; //标段
//获取标段信息
$.ajax({
    type:"GET",
    url:"./getAllsecname",
    dataType:"json",
    success:function (res) {
        if(res.code===1){
            for(var i =0; i<res.data.length;i++){
                section += `<option value=${res.data[i].name}>${res.data[i].name}</option>`;
            }
        }
    }
})
var atlasFormDom =['    <form  id="atlasform" action="#" onsubmit="return false" class="layui-form" style="padding-top: 20px;">',
    '        <input type="hidden" name="selfid" id="addId" style="display: none;">',
    '        <input type="hidden" name="id" id="editId" style="display: none;">',
    '        <div class="layui-form-item">',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">图号</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="picture_number" id="picture_number" lay-verify="required" placeholder="图号" autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">图名</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="picture_name" id="picture_name" lay-verify="required"  placeholder="图名" autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '        </div>',
    '        <hr class="layui-bg-gray">',
    '        <div class="layui-form-item">',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">图纸张数(输入数字)</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="picture_papaer_num" id="picture_papaer_num"  placeholder="图纸张数(输入数字)" autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">折合A1图纸</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="a1_picture" id="a1_picture"  lay-verify="required" placeholder="折合A1图纸" autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '        </div>',
    '        <hr class="layui-bg-gray">',
    '        <div class="layui-form-item">',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">设计</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="design_name" id="design_name"  placeholder="设计" autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">校验</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="check_name" id="check_name" placeholder="校验" autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '        </div>',
    '        <hr class="layui-bg-gray">',
    '        <div class="layui-form-item">',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">审查</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="examination_name" id="examination_name" placeholder="审查"  autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">完成日期</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="completion_date" id="completion_date" lay-verify="required" placeholder="完成日期"  autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '        </div>',
    '        <hr class="layui-bg-gray">',
    '        <div class="layui-form-item">',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">标段</label>',
    '                <div class="layui-input-inline">',
    '                    <select name="section" id="section" lay-verify="">',
    '                    </select>',
    '                </div>',
    '            </div>',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">图纸类别</label>',
    '                <div class="layui-input-inline">',
    '                    <select name="paper_category" id="paper_category" lay-verify="">',
    '                        <option value=""></option>',
    '                        <option value="A">A</option>',
    '                        <option value="B" selected="">B</option>',
    '                        <option value="C">C</option>',
    '                        <option value="D">D</option>',
    '                    </select>',
    '                </div>',
    '            </div>',
    '        </div>',
    '        <hr class="layui-bg-gray">',
    '        <div class="layui-form-item">',
    '            <div class="col-xs-12" style="text-align: center;">',
    '                <button class="layui-btn" lay-submit="" lay-filter="demo1"><i class="fa fa-save"></i> 保存</button>&nbsp;&nbsp;&nbsp;',
    '                <button type="reset" class="layui-btn layui-btn-danger layui-layer-close"><i class="fa fa-close"></i> 返回</button>',
    '            </div>',
    '        </div>',
    '    </form>',
].join(""); //图册新增dom
//layui
var drawingFormDom =['    <form  id="drawinfform" action="#" onsubmit="return false" class="layui-form" style="padding-top: 20px;">',
    '        <input type="hidden" name="selfid" id="addId" style="display: none;">',
    '        <input type="hidden" name="id" id="editId" style="display: none;">',
    '        <div class="layui-form-item">',
    '            <div class="autograph">',
    '                <label class="layui-form-label">图号</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="picture_number" id="picture_number_drawing" lay-verify="required" placeholder="图号" autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '        </div>',
    '        <hr class="layui-bg-gray">',
    '        <div class="layui-form-item">',
    '            <div class="autograph">',
    '                <label class="layui-form-label">图名</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="picture_name" id="picture_name_drawing" lay-verify="required"  placeholder="图名" autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '        </div>',
    '        <hr class="layui-bg-gray">',
    '        <div class="layui-form-item">',
    '            <div class="autograph">',
    '            <label class="layui-form-label">图纸文件</label>',
    '            <div class="layui-input-inline">',
    '                <input type="text" name="signature" id="file_name" placeholder="图纸文件" lay-verify="required" readonly autocomplete="off" class="layui-input">',
    '            </div>',
    '            <div class="layui-form-mid layui-word-aux">',
    '               <button type="button" class="layui-btn" id="upload">选择</button>',
    '            </div>',
    '            </div>',
    '         </div>',
    '        <div class="layui-form-item">',
    '            <div class="col-xs-12" style="text-align: center;">',
    '                <button class="layui-btn" lay-submit="" lay-filter="demo2"><i class="fa fa-save"></i> 保存</button>&nbsp;&nbsp;&nbsp;',
    '                <button type="reset" class="layui-btn layui-btn-danger layui-layer-close"><i class="fa fa-close"></i> 返回</button>',
    '            </div>',
    '        </div>',
    '    </form>',
].join(""); //图册新增dom
//layui
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
        console.log(data.filed);
        $.ajax({
            type: "post",
            url:"./editAtlasCate",
            data:data.field,
            success: function (res) {
                console.log(selfid)
                if(res.code == 1) {
                    var url = "/archive/common/datatablespre/tableName/atlas_cate/selfid/"+selfid+".shtml";
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
    form.on('submit(demo2)', function(data){
        console.log(data.filed);
        $.ajax({
            type: "post",
            url:"./addPicture",
            data:data.field,
            success: function (res) {
                console.log(selfid)
                if(res.code == 1) {
                    var url = "/archive/common/datatablespre/tableName/atlas_cate/selfid/"+selfid+".shtml";
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

});
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
        autoParam: ["pid","id"],
        type : "post",
        url : "./atlastree",
        dataType :"json"
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
//初始化树
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
}
//点击添加节点
function addNodetree() {
    var pid  = selfid?selfid:0;
    layer.prompt({
        title: '请输入节点名称',
    },function(value, index, elem){
        $.ajax({
            url:'./editCatetype',
            type:"post",
            data:{pid:pid,name:value},
            success: function (res) {
                if(res.code===1){
                    if(sNodes){
                        zTreeObj.addNodes(sNodes[0],res.data);
                    }else{
                        zTreeObj.addNodes(null,res.data);
                    }

                }
            }
        });
        layer.close(index);
    });

};
//编辑节点
function editNodetree() {
    if(!selfid){
        layer.msg("请选择节点",{time:1500,shade: 0.1});
        return;
    }
    console.log(sNodes[0])
    layer.prompt({
        title: '编辑',
        value:sNodes[0].name
    },function(value, index, elem){
        $.ajax({
            url:'./editCatetype',
            type:"post",
            data:{id:selfid,name:value},
            success: function (res) {
                if(res.code===1){
                    sNodes[0].name = value;
                    zTreeObj.updateNode(sNodes[0]);//更新节点名称
                    layer.msg("编辑成功")
                }
            }
        });
        layer.close(index);
    });
};
//删除节点
function delNodetree() {
    if(!selfid){
        layer.msg("请选择节点");
        return;
    }
    if(!sNodes[0].children){
        layer.confirm("该操作会将关联数据同步删除，是否确认删除？",function () {
            $.ajax({
                url:'./delCatetype',
                type:"post",
                data:{id:selfid},
                success: function (res) {
                    if(res.code===1){
                        layer.msg("删除节点成功",{time:1500,shade: 0.1});
                        var url = "/admin/common/datatablespre/tableName/admin_cate/id/"+selfid+".shtml";
                        tableItem.ajax.url(url).load();
                        zTreeObj.removeNode(sNodes[0]);
                        selfid = "";
                    }
                }
            });
        });
    }else{
        layer.msg("包含下级，无法删除",{time:1500,shade: 0.1});
    }

};
//
//全部展开
$('#openNode').click(function(){
    zTreeObj.expandAll(true);
});

//收起所有
$('#closeNode').click(function(){
    zTreeObj.expandAll(false);
});

//节点移动方法
function moveNode(zTreeObj,selectNode,state) {
    var changeNode;
    var change_sort_id; //发生改变的排序id
    var change_id; //发生改变的id

    var select_sort_id = selectNode.sort_id;//选中的排序id
    var select_id = selectNode.id;//选中的id
    if(state === "next"){
        changeNode = selectNode.getNextNode();
        if (!changeNode){
            layer.msg('已经移到底啦');
            return false;
        }
    }else if(state === "prev"){
        changeNode = selectNode.getPreNode();
        if (!changeNode){
            layer.msg('已经移到顶啦');
            return false;
        }
    }
    change_id = changeNode.id;
    change_sort_id = changeNode.sort_id;
    console.log();
    $.ajax({
        url: "./sortNode",
        type: "post",
        data: {
            change_id:change_id, //影响节点id
            change_sort_id:change_sort_id, //影响节点sort_id
            select_id:select_id,//移动节点id
            select_sort_id:select_sort_id,//移动节点sort_id
        },
        dataType: "json",
        success: function (res) {
            zTreeObj.moveNode(changeNode, selectNode, state);
            changeNode.sort_id = select_sort_id;
            selectNode.sort_id = change_sort_id;
        }
    });
}
// 点击节点移动
function getmoveNode(state) {
    if(sNodes===undefined||sNodes.length<=0){
        layer.msg("请选择节点");
        return;
    }

    moveNode(zTreeObj,sNodes[0],state);
}
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
    $(".path").html("");
    $(this).addClass("select-color").siblings().removeClass("select-color");
    if($(this).hasClass("drawing")){
        isAtlas = false;
    }else{
        isAtlas = true;
        $(".path").html("");
        $(this).addClass("select-color").siblings().removeClass("select-color");
        selectData = tableItem.row(".select-color").data();//获取选中行数据
        $(".path").html($(".layout-panel-center .panel-title").html().split("-").pop()+"-"+selectData[1]);
    }
});
//点击编辑图册
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
                $("#editId").val(res.data.id)
            }
        }
    })
}
//点击删除图册
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
//预览图片
function conPicshow(id){
    var index = top.layer.open({
        type: 2,
        title: '图片预览',
        shadeClose: true,
        shade: 0.8,
        area: ['880px', '600px'],
        content: '/pictureshow'//iframe的url
    });
    top.layer.full(index);
        // $.ajax({
        //     type: "Get",
        //     url: "/Atlas/IsDown?atlasId=" + id + "&_t=" + new Date().getTime(),
        //     success: function (data) {
        //         if (data.result == "Faild") {
        //             layerAlert("你没有预览下载此文件");
        //         }
        //         else {
        //             var index = top.layer.open({
        //                 type: 2,
        //                 title: '图片预览',
        //                 shadeClose: true,
        //                 shade: 0.8,
        //                 area: ['880px', '600px'],
        //                 content: '/Model/PictureShow?type=2&elementId=' + id //iframe的url
        //             });
        //
        //             top.layer.full(index);
        //         }
        //     }
        // });
}