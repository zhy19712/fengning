var selfid,zTreeObj,groupid,sNodes,selectData="";//选中的节点id，ztree对象，父节点id，选中的节点，选中的表格的信息
var atlasFormDom =['    <form  id="atlasform" action="#" onsubmit="return false" class="layui-form" style="padding-top: 20px;">',
    '        <input type="hidden" name="id" id="addId" style="display: none;">',
    '        <div class="layui-form-item">',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">图号</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="number_id" id="number_id" lay-verify="required" placeholder="图号" autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">图名</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="role_name" id="role_name" lay-verify="required" lay-verify="required" placeholder="图名" autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '        </div>',
    '        <hr class="layui-bg-gray">',
    '        <div class="layui-form-item">',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">图纸张数(输入数字)</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="number_id" id="number_id" lay-verify="required" placeholder="图纸张数(输入数字)" autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">折合A1图纸</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="role_name" id="role_name" lay-verify="required" lay-verify="required" placeholder="折合A1图纸" autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '        </div>',
    '        <hr class="layui-bg-gray">',
    '        <div class="layui-form-item">',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">设计</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="number_id" id="number_id" lay-verify="required" placeholder="设计" autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">校验</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="role_name" id="role_name" lay-verify="required" lay-verify="required" placeholder="校验" autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '        </div>',
    '        <hr class="layui-bg-gray">',
    '        <div class="layui-form-item">',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">审查</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="create_owner" id="create_owner" placeholder="审查"  autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">完成日期</label>',
    '                <div class="layui-input-inline">',
    '                    <input type="text" name="create_time" id="create_time" placeholder="完成日期"  autocomplete="off" class="layui-input">',
    '                </div>',
    '            </div>',
    '        </div>',
    '        <hr class="layui-bg-gray">',
    '        <div class="layui-form-item">',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">标段</label>',
    '                <div class="layui-input-inline">',
    '                    <select name="interest" lay-verify="">',
    '                        <option value=""></option>',
    '                        <option value="0">写作</option>',
    '                        <option value="1" selected="">111</option>',
    '                        <option value="2">游戏</option>',
    '                        <option value="3">音乐</option>',
    '                        <option value="4">旅行</option>',
    '                    </select>',
    '                </div>',
    '            </div>',
    '            <div class="layui-inline">',
    '                <label class="layui-form-label">图纸类别</label>',
    '                <div class="layui-input-inline">',
    '                    <select name="interest" lay-verify="">',
    '                        <option value=""></option>',
    '                        <option value="0">写作</option>',
    '                        <option value="1" selected="">111</option>',
    '                        <option value="2">游戏</option>',
    '                        <option value="3">音乐</option>',
    '                        <option value="4">旅行</option>',
    '                    </select>',
    '                </div>',
    '            </div>',
    '        </div>',
    '        <hr class="layui-bg-gray">',
    '        <div class="layui-form-item">',
    '            <div class="col-xs-12" style="text-align: center;">',
    '                <button class="layui-btn" lay-submit="" lay-filter="demo1"><i class="fa fa-save"></i> 保存</button>&nbsp;&nbsp;&nbsp;',
    '                <button type="reset" class="layui-btn layui-btn-danger"><i class="fa fa-close"></i> 返回</button>',
    '            </div>',
    '        </div>',
    '    </form>',
].join(""); //图册新增dom
//layui
layui.use(['element',"layer",'form','laydate'], function(){
    var $ = layui.jquery
        ,element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块

    var form = layui.form
        ,layer = layui.layer
        ,layedit = layui.layedit
        ,laydate = layui.laydate;

    //监听提交
    form.on('submit(demo1)', function(data){
        $.ajax({
            type: "post",
            url:"./editCate",
            data:data.field,
            success: function (res) {
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
    var url = "/admin/common/datatablespre/tableName/admin_cate/id/"+selfid+".shtml";
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
