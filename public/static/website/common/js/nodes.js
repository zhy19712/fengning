;(function($){
    /**
     * 点击节点
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
            tablePath:'/admin/common/datatablesPre?tableName=admin',
            parentShow:true,
            tableItem:tableItem,
            isLoadPath:true,
            others:function () {}
        }

        $.extend(option,options);

        //加载表格数据
        function loadData() {
            $('#tableItem_wrapper').show();
            $('.tbcontainer').show();
            option.tableItem.ajax.url(option.tablePath+"&id="+ options.treeNode.id).load();
            getPath();
        }

        //获取路径
        function getPath() {
            if(option.isLoadPath){
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
        }

        //是否允许父节点加载表格数据
        if(options.treeNode.isParent){
            if(option.parentShow){
                loadData();
            }else{
                $('#tableItem_wrapper').hide();
                $('.tbcontainer').hide();
                getPath();
                return false;
            }
        }
        loadData();
        option.others();
    };
    /**
     * 展开/收起
     * @param options
     */
    $.toggle = function (options) {
        var treeObj = $.fn.zTree.getZTreeObj(options.treeId);
        treeObj.expandAll(options.state);
    };
    /**
     * 添加节点
     * @param options
     */
    $.addMode = function (options) {
        var option = {
            layerId:'3',
            area:['660px','200px'],
            title:'新增节点',
            btn: ['保存', '关闭'],
        };
        $.extend(option,options);
        layer.open({
            id:option.layerId,
            type:'1',
            area:option.area,
            title:option.title,
            btn: option.btn,
            content: $('#'+option.formId),
            yes: function(index, layero){
                console.log(index,layero);
                var newName = $(layero).find('input[name="name"]').val();
                $.ajax({
                    url:'./editNode',
                    dataType:'JSON',
                    type:'POST',
                    data:{
                        pid:nodes[0].id,
                        name:newName
                    },
                    success:function(data){
                        $('input[type="hidden"][name="treeId"]').val(data.data);
                        treeObj.addNodes(nodes[0], data.data);
                    }
                });
                $('#addNodeForm')[0].reset();
                layer.close(index);
            }
        });
    };
    /**
     * 删除节点
     * @param options[树ID,是否删除父节点,ajax请求地址,ajax参数]
     * @author wyang
     */
    $.delnode=function(options){
        var option = {
            treeId:'ztree',
            delParent:false,
            url:'./delNode',
            data:{}
        }

        var treeObj = $.fn.zTree.getZTreeObj(option.treeId);
        var nodes = treeObj.getSelectedNodes();
        var id = nodes[0].id;
        var isParent;

        option.data = { id:id };

        $.extend(option,options);

        //是否选择节点
        if(nodes==''){
            layer.msg('请选择节点');
            return false;
        }
        //是否是父节点
        if (nodes.length > 0) {
            isParent = nodes[0].isParent;
        }

        //禁止删除父节点
        if(!option.delParent){
            if(isParent){
                layer.msg('包含下级，无法删除。');
                return false
            }
        }

        layer.confirm('该操作会将关联用户同步删除，是否确认删除？',{
            icon:3,
            title:'提示'
        },function(index){
            $.ajax({
                url:option.url,
                dataType:'JSON',
                type:'POST',
                data:option.data,
                success:function(res){
                    for (var i=0, l=nodes.length; i < l; i++){
                        treeObj.removeNode(nodes[i]);
                    }
                    layer.msg(res.msg);
                }
            });
            layer.close(index);
        });
    }
})(jQuery);