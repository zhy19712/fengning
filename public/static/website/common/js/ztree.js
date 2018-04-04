;(function($){
    $.ztree = function (options) {
        var option = {
            treeId:'ztree'
        };
        $.extend(option,options);
        var setting = {
            async: {
                enable : true,
                type : "post",
                url : "./index",
                dataType :"json"
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
                onClick:zTreeOnClick,
            },
            showLine:true,
            showTitle:true,
            showIcon:true
        };

        $.extend(setting,options);

        zTreeObj = $.fn.zTree.init($("#"+ option.treeId), setting, null);
    }
})(jQuery);