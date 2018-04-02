;(function($){
    /**
     * 新增弹层
     * @param options
     * @author wyang
     */
    $.add = function(options){
        var option = {
            btn:'add',
            formId:'formlayer',
            content:$('#formlayer'),
            layerId:'1',
            area:['660px','700px']
        };
        $.extend(option,options);
        $('#'+option.btn).on('click',function(){
            var nodeId = window.nodeId;
            if(!nodeId){
                layer.msg('请选择节点');
                return false;
            }
            layer.open({
                id:option.layerId,
                type:'1',
                area:option.area,
                title:'新增',
                content:option.content,
                success:function(){

                },
                cancel: function(index, layero){
                    $('#'+option.formId)[0].reset();
                }
            });
        });
    };
    /**
     * 关闭弹层
     * @param btn/formId/others
     * @author wyang
     */
    $.close = function(options){
        var option = {
            btn:'close',
            formId:'formlayer',
            others:function(){}
        };
        $.extend(option,options);
        $('.'+option.btn).click(function () {
            $('#'+option.formId)[0].reset();
            option.others();    //关闭的时候需要处理的其他事务
            layer.closeAll('page');
        });
    };
    /**
     *  表单提交
     * @param formId/tablePath/data/ajaxUrl/others
     * @author wyang
     */
    $.submit= function (options) {
        var option = {
            formId:'formlayer',
            ajaxUrl:'./publish',
            data:{},
            others:function(){}

        };
        $.extend(option,options);
        layui.use('form',function(){
            var form = layui.form;
            //表单提交
            form.on('submit(save)', function(data){
                option.data.nodeId = window.nodeId;
                $.extend(true,data.field,option.data);
                $.ajax({
                    url:option.ajaxUrl,
                    dataType:'JSON',
                    type:'POST',
                    data:data.field,
                    success:function(res){
                        var filter = $(data.elem).attr('lay-filter');
                        if(filter=='save'){
                            layer.closeAll('page');
                        }
                        $('#'+option.formId)[0].reset();
                        option.others();    //表单提交成功后需要处理的事务
                        layer.msg(res.msg);
                        tableItem.ajax.url(options.tablePath+"&id="+ nodeId).load();
                    }
                });
                return false;
            });
        });
    }
})(jQuery);