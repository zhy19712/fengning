;(function($){
    /**
     * 上传
     * @param options
     */
    $.upload = function (options) {
        var option = {
            btnId:'#upload',
            btnText:'上传',
            server: "/standard/common/upload",
            inputName:'file_name',
            others:function(){}
        };
        $.extend(option,options);
        uploader = WebUploader.create({
            auto: true,
            // swf文件路径
            swf:  '/static/public/webupload/Uploader.swf',

            // 文件接收服务端。
            server: option.server,

            // 选择文件的按钮。可选。
            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: {
                multiple: false,
                id: option.btnId,
                innerHTML: option.btnText
            },
            formData:option.formData,
            accept: option.accept,
            // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
            resize: false
        });
        uploader.on( 'uploadSuccess', function( file ,res) {
            layer.msg(file.name+'已上传成功');
            $('input[name='+ option.inputName +']').val(file.name);
            window.file_id = res.id;
        });
    }
})(jQuery);