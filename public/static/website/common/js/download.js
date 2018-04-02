;(function($){
    $.download = function(options){
        var file_id = $(options.that).attr('uid');
        var option = {
            url:'',
            tablePath:'',
            data:{
                file_id:file_id
            }
        }
        $.extend(option,options);
        $.ajax({
            url: option.url,
            type: "post",
            dataType: "json",
            data:option.data,
            success: function (res) {
                if(res.code != 1){
                    layer.msg(res.msg);
                }else {
                    $("#form_container").empty();
                    var str = "";
                    str += ""
                        + "<iframe name=downloadFrame"+ id +" style='display:none;'></iframe>"
                        + "<form id=download"+ id +" action="+ option.tablePath +" method='get' target=downloadFrame"+ id +">"
                        + "<span class='file_name' style='color: #000;'>"+str+"</span>"
                        + "<input class='file_url' style='display: none;' name='major_key' value="+ id +">"
                        + "<button type='submit' class=btn" + id +"></button>"
                        + "</form>"
                    $("#form_container").append(str);
                    $("#form_container").find(".btn" + id).click();
                }
            }
        })
    }
})(jQuery);