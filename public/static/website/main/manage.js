layui.use('element', function(){
    var element = layui.element;
});

function easyUiPanelToggle() {
    var number = $("#easyuiLayout").layout("panel", "east")[0].clientWidth;
    if(number<=0){
        $('#easyuiLayout').layout('expand','east');
    }
}