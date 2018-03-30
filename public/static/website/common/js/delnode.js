;(function($){
    /**
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
                success:function(){
                    for (var i=0, l=nodes.length; i < l; i++){
                        treeObj.removeNode(nodes[i]);
                    }
                    layer.msg('删除成功！', {icon: 1});
                }
            });
            layer.close(index);
        });
    }
})(jQuery);