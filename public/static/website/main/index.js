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
                        '<i class="fa fa-close"></i>' +
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

