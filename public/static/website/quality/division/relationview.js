var unitEnginNoId = document.cookie.split(';')[0].split('=')[1]

$.ajax({
    url: "./openModelPicture",
    type: "post",
    dataType: "json",
    data:{
        id:window.unitEnginNoId
    },
    success: function (res) {
        var nodeStr = res.nodeStr;
        var nodes =  JSON.parse(nodeStr);
        setZtree(nodes);
    }
});

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

function zTreeOnCheck(event, treeId, treeNode) {
    var node = [];
    var treeObj = $.fn.zTree.getZTreeObj("ztree");
    var nodes = treeObj.getCheckedNodes(true);
    for(var i = 0;i<nodes.length;i++){
        if(nodes[i].isParent==undefined||nodes[i].isParent==false){
            node.push(nodes[i]);
        }
    }
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
                console.log(treeNode);
            }
        },
        showLine:true,
        showTitle:true,
        showIcon:true
    };

    zTreeObj = $.fn.zTree.init($("#selectZtree"), setting, node);
}

function zTreeOnClick(event, treeId, treeNode) {
    console.log(treeNode);
    loadModel();
}