$.ztree({
    //点击节点
    zTreeOnClick:function (event, treeId, treeNode){
        tableInfo();
        $.clicknode({
            tableItem:tableItem,
            treeNode:treeNode,
            tablePath:'/quality/common/datatablesPre?tableName=quality_unit',
            isLoadPath:false
        });
        $.ajax({
            url: "./unitPlanning",
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
                    var pointItemArr = ['<a href="javascript:;" class="pointItem" onclick="loadTableData(this)"><i class="fa fa-file-text"></i>作业</a>'];
                    for(var i in res.data){
                        pointItemArr.push('<a href="javascript:;" class="pointItem" onclick="loadTableData(this)"><i class="fa fa-file-text"></i>'+ res.data[i] +'</a><i class="fa fa-long-arrow-right"></i>');
                    }
                    $('#controlPointItem').append(pointItemArr.join(''));
                    $('.pointItem:last').next('i').remove();
                }
            }
        });
    }
});

function loadTableData(that) {
    $(that).addClass('active').siblings('a').removeClass();
    tableInfo();
}