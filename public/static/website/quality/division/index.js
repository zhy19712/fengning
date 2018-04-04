//初始化layui组件
var initUi = layui.use('form','laydate');
//工程标准及规范树
$.ztree();
//点击节点
function zTreeOnClick(event, treeId, treeNode){
    $.clicknode({
        treeNode:treeNode,
        tablePath:'/quality/common/datatablesPre?tableName=quality_unit',
        isLoadPath:false
    });
};

//全部展开
$('#openNode').click(function(){
    $.toggle({
        treeId:'ztree',
        state:true
    });
});

//收起所有
$('#closeNode').click(function(){
    $.toggle({
        treeId:'ztree',
        state:false
    });
});

//删除节点
$('#delNode').click(function () {
    $.delnode();
});
//新增节点弹层
$('#addNode').click(function (){
    if(!window.treeNode||window.treeNode.level==0){
        layer.msg('未选择标段');
        return false;
    }
    $.addNode({
        area:['670px','420px'],
        others:function () {
            //构建select
            //type = 1 单位工程 / type = 2 [子单位工程|分部工程] / type = 3 [子分部工程|分项工程]
            var options = [];
            var unitArr = [{type:1,name:"单位工程"}];
            var branchArr = [{type:2,name:"子单位工程"},{type:2,name:"分部工程"}];
            var itemArr = [{type:3,name:"子分部工程"},{type:3,name:"分项工程"},{type:1,name:"单位工程"}];
            if(window.treeNode.level==1){
                options.push('<option value='+ unitArr[0].type +'>'+ unitArr[0].name +'</option>');
            }
            if(window.treeNode.level==2){
                for(var i = 0;i<branchArr.length;i++){
                    options.push('<option value='+ branchArr[i].type +'>'+ branchArr[i].name +'</option>');
                }
            }
            if(window.treeNode.level==3){
                for(var i = 0;i<itemArr.length;i++){
                    options.push('<option value='+ itemArr[i].type +'>'+ itemArr[i].name +'</option>');
                }
            }
            $('select[name="type"]').empty();
            $('select[name="type"]').append(options);

            if(window.treeNode.level>2){
                $('.autograph').show();
            }
            initUi.form.render('select');
        }
    });
});
//工程类型树
$.ztree({
    treeId:'typeZtree'
});
//展示工程类型树
$('#typeZtreeBtn').click(function () {
    if(window.treeNode.level>2){
        layer.open({
            title:'工程类型',
            id:'99',
            type:'1',
            content:$('#ztreeLayer')
        });
    }

});
//编辑节点
$('#editNode').click(function () {
    $.editNode({
        area:['670px','420px'],
    });
});
//关闭弹层
$.close({
    formId:'nodeForm'
});
//提交节点变更
$('#save').click(function () {
    var d_code = $('input[name="d_code"]').val();
    var d_name = $('input[name="d_name"]').val();
    var type = $('select[name="type"] option:selected').val();
    var primary = $('input[name="primary"]').val();
    var remark = $('textarea[name="remark"]').val();
    if(window.treeNode.level>0){
        var section_id = window.treeNode.section_id;
    }
    $.submitNode({
        data:{
            d_code:d_code,
            d_name:d_name,
            type:type,
            primary:primary,
            remark:remark,
            section_id:section_id
        }
    });
});