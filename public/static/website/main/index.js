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
    layer.open({
        title:'人员选择',
        id:'100',
        type:'1',
        area:['750px','550px'],
        content:$('#selectUser'),
        success:function () {
            initZtree();
        },
        cancel: function(index, layero){
            layer.close(layer.index);
        }
    });
});


//字符解码
function ajaxDataFilter(treeId, parentNode, responseData) {

    if (responseData) {
        for(var i =0; i < responseData.length; i++) {
            responseData[i] = JSON.parse(responseData[i]);
            responseData[i].name = decodeURIComponent(responseData[i].name);
        }
    }
    return responseData;
}

function initZtree() {
    var setting = {
        async: {
            enable : true,
            autoParam: ["pid"],
            type : "post",
            url : "../../archive/Atlas/getOrganization",
            dataType :"json",
            dataFilter: ajaxDataFilter
        },
        data: {
            simpleData: {
                enable: true,
                idKey: "id",
                pIdKey: "pid"
            }
        },
        view:{
            selectedMulti: false
        },
        callback:{
            onDblClick: zTreeOnDblClick
        },
        showLine:true,
        showTitle:true,
        showIcon:true
    };
    zTreeObj = $.fn.zTree.init($("#userZtree"), setting, null);
}


function zTreeOnDblClick(event, treeId, treeNode) {

}