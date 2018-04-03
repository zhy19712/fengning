//初始化layui组件
var initUi = layui.use('form','laydate');
//工程标准及规范树
$.ztree();
//点击节点
function zTreeOnClick(event, treeId, treeNode){
    $.clicknode({
        treeNode:treeNode,
        tablePath:'/quality/common/datatablesPre?tableName=quality_unit',
        isLoadPath:false
    });
};

//全部展开
$('#openNode').click(function(){
    $.toggle({
        treeId:'ztree',
        state:true
    });
});

//收起所有
$('#closeNode').click(function(){
    $.toggle({
        treeId:'ztree',
        state:false
    });
});

//删除节点
$('#delNode').click(function () {
    $.delnode();
});

$('#addNode').click(function (){
    $.addMode({
        formId:'addNodeForm'
    });
});