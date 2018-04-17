//初始化layui组件
var initUi = layui.use('form','laydate');
var form = layui.form;

//ztree
$.ztree({
    //点击节点
    zTreeOnClick:function (event, treeId, treeNode){
        $('#enginId').val(treeNode.add_id);
        $.clicknode({
            tableItem:tableItem,
            treeNode:treeNode,
            isLoadPath:false,
            isLoadTable:false,
            parentShow:false
        });
        var iShow = treeNode.edit_id;
        if(iShow){
            $.ajax({
                url: "./productionProcesses",
                type: "post",
                data: {
                    add_id:window.treeNode.add_id
                },
                dataType: "json",
                success: function (res) {
                    $('#controlPointItem').empty();
                    if(res.code=1){
                        if(res.data==''){
                            return false;
                        }
                        var pointItemArr = [];
                        for(var i in res.data){
                            pointItemArr.push('<a href="javascript:;" class="pointItem" uid='+ i +' onclick="loadTableData(this)"><i class="fa fa-file-text-o"></i>'+ res.data[i] +'</a><i class="fa fa-long-arrow-right"></i>');
                        }
                        $('#controlPointItem').append(pointItemArr.join(''));
                        $('.pointItem:last').next('i').remove();
                        $('#controlPointItem a:first').click();
                    }
                }
            });
        }
    }
});

//控制点数据
function loadTableData(that) {
    var nodeId = window.treeNode.add_id;
    var workId = $(that).attr('uid');
    $('#workId').val(workId);
    window.tableItem.ajax.url('/quality/common/datatablesPre?tableName=unit_quality_control&id='+ nodeId +'&workId='+ workId +'').load();
    $('#tableItem_wrapper,.tbcontainer,#subList').show();
    $(that).addClass('active').siblings('a').removeClass();
    btnToggle(that);
}

//按钮切换展示
function btnToggle(that) {
    if($(that).index()==0){
        $('.subBtn').hide();
        $('.frtBtn').show();
    }else{
        $('.frtBtn').hide();
        $('.subBtn').show();
    }
}
