;(function($){
    /**
     * @param options[当前节点,表格实例,是否允许父节点加载表格数据,表格数据请求路径]
     * @author wyang
     */
    $.clicknode = function(options){
        var prevNode = options.treeNode.getPreNode();
        var nextNode = options.treeNode.getNextNode();
        var parentNode = options.treeNode.getParentNode();

        if (parentNode){
            window.parentNodeId = options.treeNode.pId;
        }

        if(prevNode){
            window.prevNodeId = prevNode.id;
            window.prevSortId = prevNode.sort_id;
        }

        if(nextNode){
            window.nextNodeId = nextNode.id;
            window.nextSortId = nextNode.sort_id;
        }

        window.nodeId = options.treeNode.id;
        window.nodeSortId = options.treeNode.sort_id;

        console.log(options.treeNode);

        var option = {
            tablePath:'/admin/common/datatablesPre?tableName=admin'
        }

        $.extend(option,options);

        //加载表格数据
        function loadData() {
            $('#tableItem_wrapper').show();
            $('.tbcontainer').show();
            options.tableItem.ajax.url(option.tablePath+"&id="+ options.treeNode.id).load();
            getPath();
        }

        //获取路径
        function getPath() {
            $.ajax({
                url: "./getParents",
                type: "post",
                data: {id:options.treeNode.id},
                dataType: "json",
                success: function (res) {
                    if(res.msg === "success"){
                        $("#path").text("当前路径:" + res.path)
                    }
                }
            });
        }

        //是否允许父节点加载表格数据
        if(options.treeNode.isParent){
            if(options.parentShow){
                loadData();
            }else{
                $('#tableItem_wrapper').hide();
                $('.tbcontainer').hide();
                getPath();
                return false;
            }
        }
        loadData();
    }
})(jQuery);