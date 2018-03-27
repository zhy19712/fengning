
var selfid,zTreeObj,groupid,sNodes;
//时间
Date.prototype.Format = function (fmt) { // author: meizz
    var o = {
        "M+": this.getMonth() + 1, // 月份
        "d+": this.getDate(), // 日
    };
    if (/(y+)/.test(fmt))
        fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
};

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


//新增
var dom = ['    <form  id="roleform" action="#" onsubmit="return false" class="layui-form" style="padding-top: 20px;">',
    '        <input type="hidden" name="id" id="addId" style="display: none;">',
    '        <input type="hidden" name="pid" id="pid" style="display: none;">',
    '        <div class="layui-form-item">',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">编号</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="number_id" id="number_id" lay-verify="required" placeholder="编号" autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">角色名称</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="role_name" id="role_name" lay-verify="required" lay-verify="required" placeholder="角色名称" autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '        </div>',
    '        <hr class="layui-bg-gray">',
    '        <div class="layui-form-item">',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">创建人</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="create_owner" id="create_owner" placeholder="创建人" readonly autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">创建时间</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="create_time" id="create_time" placeholder="创建时间" readonly autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '        </div>',
    '        <hr class="layui-bg-gray">',
    '        <div class="layui-form-item">',
    '                <label class="layui-form-label">角色描述</label>',
    '                <div class="layui-input-block">',
    '                       <textarea name="desc" id="desc"  placeholder="角色描述" placeholder="请输入" class="layui-textarea"></textarea>',
    '    </div>',
    '        </div>',
    '        <hr class="layui-bg-gray">',
    '        <div class="layui-form-item">',
    '            <div class="col-xs-12" style="text-align: center;">',
    '                <button class="layui-btn" lay-submit="" lay-filter="demo1"><i class="fa fa-save"></i> 保存</button>&nbsp;&nbsp;&nbsp;',
    '                <button type="reset" class="layui-btn layui-btn-danger layui-layer-close layui-layer-close1"><i class="fa fa-close"></i> 返回</button>',
    '            </div>',
    '        </div>',
    '    </form>',
].join("");


//tab 切换
layui.use(['element',"layer",'form'], function(){
    var $ = layui.jquery
        ,element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块

    var form = layui.form
        ,layer = layui.layer
        ,layedit = layui.layedit
        ,laydate = layui.laydate;

    //监听提交
    form.on('submit(demo1)', function(data){
            console.log(selfid);
            console.log(data.field);
            $.ajax({
                type: "post",
                url:"./editCate",
                data:data.field,
                success: function (res) {
                    console.log(res)
                    if(res.code == 1) {
                        var url = "/admin/common/datatablespre/tableName/admin_cate/id/"+selfid+".shtml";
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
});

//点击获取路径
function onClick(e, treeId, node) {
    $(".layout-panel-center .panel-title").text("");
    sNodes = zTreeObj.getSelectedNodes();//选中节点
    selfid = zTreeObj.getSelectedNodes()[0].id
    var path = sNodes[0].name; //选中节点的名字
    node = sNodes[0].getParentNode();//获取父节点
    //判断是否还有父节点
    if (node) {
        //判断是否还有父节点
        while (node){
            path = node.name + ">>" + path;
            node = node.getParentNode();
        }
    }else{
        $(".layout-panel-center .panel-title").text(sNodes[0].name);
    }
    //判断是否拉数据
    groupid = sNodes[0].pId //父节点的id
    if (!sNodes[0].children){
        var url = "/admin/common/datatablespre/tableName/admin_cate/id/"+selfid+".shtml";
        tableItem.ajax.url(url).load();
    }
    $(".layout-panel-center .panel-title").text("当前路径:"+path)
}
//点击添加 删除 编辑节点
function addNodetree() {
    $.ajax({
        url:'./editCatetype',
        type:"post",
        data:{id:selfid,pid:groupid,name:sNodes[0].name},
        success: function (res) {
            if(res.code===1){
                layer.msg("删除节点成功",{time:1500,shade: 0.1});
                zTreeObj.reAsyncChildNodes(null, "refresh");
            }
        }
    });
};

function editNodetree() {
    if(!selfid){
        layer.msg("请选择节点",{time:1500,shade: 0.1});
        return;
    }
    // $.ajax({
    //     url: "getOneNode",
    //     type: "post",
    //     data: {id:selfid},
    //     dataType: "json",
    //     success: function (res) {
    //         if(sNodes.level == 0) {
    //             $("#level2 input").val("");
    //             $("#treePid").val(mytree_node.pId);
    //             $("#id2").val(mytree_node.id);
    //             $("#companyName").val(res.pname);
    //             $("#level2 h5").text('编辑');
    //             $("#level2").modal('show');
    //         }else if(sNodes.level == 1) {
    //             $("#level2 input").val("");
    //             $("#treePid").val(mytree_node.pId);
    //             $("#id2").val(mytree_node.id);
    //             $("#companyName").val(res.pname);
    //             $("#level2 h5").text('编辑');
    //             $("#level2").modal('show');
    //         }
    //     }
    // })
};
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
                        zTreeObj.reAsyncChildNodes(null, "refresh");
                    }
                }
            });
        });
    }else{
        layer.msg("包含下级，无法删除",{time:1500,shade: 0.1});
    }

};
//
//编辑
function conEdit(id){
    $.ajax({
        url: "./getOne",
        type: "post",
        data: {id:id},
        dataType: "json",
        success: function (res) {
            console.log(res);
            var nowtime = new Date().Format("yyyy-MM-dd");
            layer.open({
                type: 1,
                title: '角色管理—新增',
                area: ['690px', '440px'],
                content:dom
            });
            $("#addId").val(res.data.id);
            $("#number_id").val(res.data.number_id);
            $("#role_name").val(res.data.role_name);
            $("#desc").val(res.data.desc);
            $('#create_owner').val(res.data.create_owner);
            $('#create_time').val(res.data.create_time);
        }
    })
}
//删除
function conDel(id){
    layer.confirm('该操作会将关联数据同步删除，是否确认删除？', function(index){
        console.log(id);
        $.ajax({
            url: "./delCate",
            type: "post",
            data: {id:id},
            dataType: "json",
            success: function (res) {
                console.log(res);
                if(res.code == 1){
                    layer.msg("删除成功！");
                    var url = "/admin/common/datatablespre/tableName/admin_cate/id/"+selfid+".shtml";
                    tableItem.ajax.url(url).load();
                }
            }
        });
        layer.close(index);
    });
}
