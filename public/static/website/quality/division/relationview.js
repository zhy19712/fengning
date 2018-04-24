var unitEnginNoId = document.cookie.split(';')[0].split('=')[1];    //单元工程段号编号
var picture_id; //模型主键
//加载构件树
var modelId = [];
$.ajax({
    url: "./openModelPicture",
    type: "post",
    dataType: "json",
    data:{
        id:window.unitEnginNoId
    },
    success: function (res) {
        var nodes = JSON.parse(res.data)
        setZtree(nodes);
    }
});

//构建构件树
function setZtree(nodes) {
    var setting = {
        check: {
            enable: true
        },
        data: {
            simpleData: {
                enable: true,
                idKey: "id",
                pIdKey: "pId"
            }
        },
        view:{
            selectedMulti: false
        },
        callback:{
            onCheck: zTreeOnCheck,
            onClick: zTreeOnClick
        },
        showLine:true,
        showTitle:true,
        showIcon:true
    };

    zTreeObj = $.fn.zTree.init($("#ztree"), setting, nodes);
}

//加载模型视图
function zTreeOnCheck(event, treeId, treeNode) {
    console.log(treeNode);
    picture_id = treeNode.picture_id;
    //勾选定位模型
    /*if(treeNode.checked){
        loadModel(treeNode.add_id);
    }*/
    // TODO:一对多
    /*var node = [];
    var treeObj = $.fn.zTree.getZTreeObj("ztree");
    var nodes = treeObj.getCheckedNodes(true);
    for(var i = 0;i<nodes.length;i++){
        if(nodes[i].isParent==undefined||nodes[i].isParent==false){
            node.push(nodes[i]);
        }
    }
    creatSelectedZtree(node);*/

    // TODO:一对一
    var node = [];
    var treeObj = $.fn.zTree.getZTreeObj("ztree");
    var checkedNodes = treeObj.getCheckedNodes(true);
    for(var i = 0;i<checkedNodes.length;i++){
        treeObj.checkNode(checkedNodes[i],false,false);
    }
    node.push(treeNode);
    treeObj.checkNode(treeNode,true,false);
    creatSelectedZtree(node);
}

//构建已选构件树
window.creatSelectedZtree = function (node,uObjSubID) {
    var setting = {
        data: {
            simpleData: {
                enable: true,
                idKey: "id",
                pIdKey: "pId"
            }
        },
        view:{
            selectedMulti: false
        },
        callback:{
            onClick: function (event, treeId, treeNode) {
                zTreeOnClick(event, treeId, treeNode);
            }
        },
        showLine:true,
        showTitle:true,
        showIcon:true
    };
    zTreeObj = $.fn.zTree.init($("#selectZtree"), setting, node);
    var checkedNodes = zTreeObj.getCheckedNodes(true);
    $('#selectCount').text(checkedNodes.length);
}

//选中关联节点及加载模型视图
function zTreeOnClick(event, treeId, treeNode) {
    var number = treeNode.picture_number;
    var treeObj = $.fn.zTree.getZTreeObj("ztree");
    var nodes = treeObj.getNodesByParam("id",treeNode.id);
    treeObj.selectNode(nodes[0],true);
    loadModel(number);
}

$('#save').click(function () {
    saveModel(picture_id);
});