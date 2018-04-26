uObjSubIdSingle = new Number();      //已选非隐藏单个模型ID
uObjSubIDArr = [];      //已选非隐藏模型ID数组
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
});

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

$('#anchorFile li').click(function () {
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

//添加自定义属性
$('#addAttr').click(function () {
    var attrGroup = [];
    attrGroup.push('<div class="layui-input-inline attrGroup">');
    attrGroup.push('<input type="text" name="attrKey" required  lay-verify="required" placeholder="属性名" autocomplete="off" class="layui-input">');
    attrGroup.push('<input type="text" name="attrVal" required  lay-verify="required" placeholder="属性值" autocomplete="off" class="layui-input">');
    attrGroup.push('<div class="layui-form-mid layui-word-aux">');
    attrGroup.push('<i class="fa fa-check saveAttr" onclick="saveAttr(this)"></i>');
    attrGroup.push('<i class="fa fa-close closeAttr" onclick="closeAttr(this)"></i>');
    attrGroup.push('</div>');
    attrGroup.push('</div>');
    $('#attrGroup').append(attrGroup.join(' '));
});

//保存自定义属性
function saveAttr(that) {
    var picture_id = uObjSubIdSingle;
    var attrKey = $(that).parents('.attrGroup').find('input[name="attrKey"]').val();
    var attrVal = $(that).parents('.attrGroup').find('input[name="attrVal"]').val();
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
        }
    });
}

//回显自定义属性
function getAttr() {
    var picture_id = uObjSubIdSingle;
    $.ajax({
        url: "./getAttr",
        type: "post",
        data: {
            picture_id:picture_id,
        },
        dataType: "json",
        success: function (res) {
            $('#attrGroup').empty();
            var attrGroup = [];
            for(key in res.attr){
                if(res.attr.hasOwnProperty(key)){
                    var val = res.attr[key];
                    attrGroup.push('<div class="layui-input-inline attrGroup">');
                    attrGroup.push('<input type="text" name="attrKey" value='+ key +' required  lay-verify="required" placeholder="属性名" autocomplete="off" class="layui-input">');
                    attrGroup.push('<input type="text" name="attrVal" value='+ val +' required  lay-verify="required" placeholder="属性值" autocomplete="off" class="layui-input">');
                    attrGroup.push('<div class="layui-form-mid layui-word-aux">');
                    attrGroup.push('<i class="fa fa-check saveAttr" onclick="saveAttr(this)"></i>');
                    attrGroup.push('<i class="fa fa-close closeAttr" onclick="closeAttr(this)"></i>');
                    attrGroup.push('</div>');
                }
            }
            attrGroup.push('</div>');
            $('#attrGroup').append(attrGroup.join(' '));
        }
    });
}

//删除自定义属性
function closeAttr(that) {
    $(that).parents('.layui-input-inline').remove();
}

//添加备注信息
$('#addRemark').click(function () {
    if(!uObjSubIdSingle){
        layer.msg('请选择模型');
        return false;
    }
    var picture_id = uObjSubIdSingle;
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
        }
    })
});

//回显备注信息
function getRemark() {
    var picture_id = uObjSubIdSingle;
    $.ajax({
        url: "./getRemark",
        type: "post",
        data: {
            picture_id:picture_id
        },
        dataType: "json",
        success: function (res) {
            $('#remark').text(res.remark);
        }
    })
}

// 添加锚点
$('#saveAnchor').click(function () {
    var picture_id = uObjSubIdSingle;
    var anchorName = $('#anchorName').html();
    var user_name = $('div[uid="createName"]').html();
    var create_time = $('div[uid="createDate"]').html();
    var componentName = $('#componentName').html();
    var remark = $('textarea[name="anchorRemark"]').text();
    console.log(remark);
    var fObjSelX = $('#fObjSelX').val();
    var fObjSelY = $('#fObjSelY').val();
    var fObjSelZ = $('#fObjSelZ').val();
    $.ajax({
       url: "./anchorPoint",
       type: "post",
       data: {
           picture_id:picture_id,
           anchorName:anchorName,
           user_name:user_name,
           create_time:create_time,
           componentName:componentName,
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

//点击锚点
function getAnchorPoint(anchorName) {
    $.ajax({
        url: "./getAnchorPoint",
        type: "post",
        data: {
            anchorName:anchorName
        },
        dataType: "json",
        success: function (res) {
            $('#anchorName').html(res[0].anchor_name);
            $('#componentName').html(res[0].component_name);
            $('textarea[name="anchorRemark"]').text(res[0].remark);
        }
    })
}

//返回
$('#backAnchor').click(function(){
    $('#defaultAttr').show();
    $('#anchorLayer').hide();
});

//easyui面板显隐
function easyUiPanelToggle() {
    var number = $("#easyuiLayout").layout("panel", "east")[0].clientWidth;
    if(number<=0){
        $('#easyuiLayout').layout('expand','east');
    }
}

function easyUiPanelToggleSouth() {
    var number = $("#centent").layout("panel", "south")[0].clientWidth;
    if(number<=0){
        $('#centent').layout('expand','south');
    }
}

//新增图片
$.upload({
    btnId:'#addImage',
    btnText:'新增1',
    server: "./uploadAnchorPoint",
});