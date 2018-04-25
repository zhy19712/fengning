uObjSubIDArr = [];      //已选非隐藏模型ID
hiddenArr = [];         //已选隐藏模型ID
isCtrlDown = false;
$(document).keydown(function (event) {
    var KeyCode = (navigator.appname=="Netscape")?event.which:window.event.keyCode;
    if(KeyCode==17){
        isCtrlDown = true;
    }
});
$(document).keyup(function (event) {
    var KeyCode = (navigator.appname=="Netscape")?event.which:window.event.keyCode;
    if(KeyCode==17){
        isCtrlDown = false;
    }
})

//标注图片滚动
window.tagSwiper = new Swiper ('#tag', {
    nextButton: '.tag-button-next',
    prevButton: '.tag-button-prev',
    slidesPerView : 4,
    centeredSlides: false,
    paginationClickable: true,
    spaceBetween: 30,
});
//快照图片滚动
window.snapshotSwiper = new Swiper ('#snapshot', {
    nextButton: '.snapshot-button-next',
    prevButton: '.snapshot-button-prev',
    slidesPerView : 4,
    centeredSlides: false,
    paginationClickable: true,
    spaceBetween: 30,
});

//添加快照
window.addSnapshot = function (base64RenData) {
    var img =
        '<div class="swiper-slide swiperSlide">' +
            '<img src="data:image/png;base64,'+ base64PicData +'" alt="">' +
            '<div class="mask">' +
                '<div class="right">' +
                    '<a href="javascript:;" onclick="imageSaveAs(base64PicData)" title="快照另存为">' +
                        '<i class="fa fa-save"></i>' +
                    '</a>' +
                    '<a href="javascript:;" title="删除快照">' +
                        '<i class="fa fa-close" id="del"></i>' +
                    '</a>' +
                '</div>'+
                '<div class="scenter">' +
                    '<a href="javascript:;" title="查看快照">' +
                     '<i class="fa fa-search"></i>' +
                    '</a>' +
                '</div>' +
                '<div class="center">2018-04-20</div>'+
            '</div>'+
        '</div>';
    return img;
}
$('.swiper-wrapper').on('mouseover mouseleave','div',function (e) {
    if(e.type == 'mouseover'){
        $(this).find('div.mask').stop(true,true).fadeIn(500);
    }else {
        $(this).find('div.mask').stop(true,true).fadeOut(500);
    }
});

function imageSaveAs(imgURL) {
    var pagePop = window.open(imgURL, "", "width=1, height=1, top=5000, left=5000");
    for (; pagePop.document.readyState !== "complete";) {
        if (pagePop.document.readyState === "complete")
            break;
    }
    pagePop.document.execCommand("SaveAs");
    pagePop.close();
}

layui.use('element', function(){
    var element = layui.element;
});

$('#toogleAttr li').click(function () {
    var uid = $(this).attr('uid');
    $(this).addClass('active').siblings().removeClass('active');
    $('#'+ uid).show().siblings('div').hide();
});

$('#del').click(function () {
    $(this).parents('.swiper-wrapper').remove();
    $(this).parents('.swiper-wrapper').html();
});

$.upload({
    btnText:''
});

$('#at').click(function () {
    window.open("./selectperson", "人员选择", "height=560, width=1000, top=200,left=400, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no,status=no");
});

$('#addAttr').click(function () {
    var attrGroup = [];
    attrGroup.push('<div class="layui-input-inline">');
    attrGroup.push('<input type="text" name="attrKey" id="attrKey" required  lay-verify="required" placeholder="属性名" autocomplete="off" class="layui-input">');
    attrGroup.push('<input type="text" name="attrVal" id="attrVal" required  lay-verify="required" placeholder="属性值" autocomplete="off" class="layui-input">');
    attrGroup.push('<div class="layui-form-mid layui-word-aux">');
    attrGroup.push('<i class="fa fa-check saveAttr" onclick="saveAttr()"></i>');
    attrGroup.push('<i class="fa fa-close closeAttr" onclick="closeAttr(this)"></i>');
    attrGroup.push('</div>');
    attrGroup.push('</div>');

    $('#attrGroup').append(attrGroup.join(' '));
});

function saveAttr() {
    var picture_id = uObjSubIDArr;
    var attrKey = $('#attrKey').val();
    var attrVal = $('#attrVal').val();
    $.ajax({
        url: "./addAttr",
        type: "post",
        data: {
            picture_id:picture_id,
            attrKey:attrKey,
            attrVal:attrVal
        },
        dataType: "json",
        success: function (res) {
            layer.msg(res.msg);
            alert(res.msg);
        }
    });
}

function closeAttr(that) {
    $(that).parents('.layui-input-inline').remove();
}

$('#addRemark').click(function () {
    if(!uObjSubIDArr){
        layer.msg('请选择模型');
        alert('请选择模型');
        return false;
    }
    var picture_id = uObjSubIDArr;
    var remarkVal = $('#remark').text();
    $.ajax({
        url: "./addRemark",
        type: "post",
        data: {
            picture_id:picture_id,
            remark:remarkVal
        },
        dataType: "json",
        success: function (res) {
            layer.msg(res.msg);
            alert(res.msg);
        }
    })
});

// 添加锚点
$('#saveAnchor').click(function () {
    var anchorName = $('#anchorName').html();
    var componentName = $('#componentName').html();
    var createName = $('#createName').html();
    var createDate = $('#createDate').html();
    var remark = $('textarea[name="remark"]').text();
    var fObjSelX = $('#fObjSelX').val(fObjSelX);
    var fObjSelY = $('#fObjSelY').val(fObjSelY);
    var fObjSelZ = $('#fObjSelZ').val(fObjSelZ);

    $.ajax({
        url: "",
        type: "post",
        data: {
            anchorName:anchorName,
            componentName:componentName,
            createName:createName,
            createDate:createDate,
            remark:remark,
            fObjSelX:fObjSelX,
            fObjSelY:fObjSelY,
            fObjSelZ:fObjSelZ
        },
        dataType: "json",
        success: function (res) {
            layer.msg(res.msg);
        }
    });
});

//删除锚点
$('#delAnchor').click(function () {
    var anchorName = $('#anchorName').html();
    delAnchor(anchorName);
});

//返回
$('#backAnchor').click(function(){
    $('#defaultAttr').show();
    $('#anchorLayer').hide();
});
