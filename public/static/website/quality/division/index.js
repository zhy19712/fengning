//初始化layui组件
var initUi = layui.use('form','laydate');
var form = layui.form;
//工程标准及规范树
$.ztree({
    //点击节点
    zTreeOnClick:function (event, treeId, treeNode){
        $.clicknode({
            tableItem:tableItem,
            treeNode:treeNode,
            tablePath:'/quality/common/datatablesPre?tableName=quality_unit',
            isLoadPath:false
        });
    }
});

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
    if(window.treeNode.level>3){
        layer.msg('请在标段上新建');
        return false;
    }
    whetherShow();
    $('input[type="hidden"][name="edit_id"]').val('');
    $('input[type="hidden"][name="add_id"]').val(window.nodeId);
    $.addNode({
        area:['670px','420px'],
        others:function () {
            //构建select
            //type = 1 单位工程 / type = 2 [子单位工程|分部工程] / type = 3 [子分部工程|分项工程]
            var options = [];
            var unitArr = [{type:1,name:"单位工程"}];
            var branchArr = [{type:2,name:"子单位工程"},{type:2,name:"分部工程"}];
            var itemArr = [{type:3,name:"子分部工程"},{type:3,name:"分项工程"},{type:3,name:"单元工程"}];
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
var typeTreeNode;
$.ztree({
    treeId:'typeZtree',
    ajaxUrl:'./getEnType',
    type:'GET',
    zTreeOnClick:function (event, treeId, treeNode){
        typeTreeNode = treeNode;
    }
});

//是否显示工程类型
function whetherShow() {
    if(window.treeNode.level>2){
        $('#enType').show();
    }else{
        $('#enType').hide();
    }
}

//展示工程类型树
$('.typeZtreeBtn').click(function () {
    layer.open({
        title:'工程类型',
        id:'99',
        type:'1',
        area:['650px','400px'],
        content:$('#ztreeLayer'),
        btn:['保存','关闭'],
        yes:function () {
            if(typeTreeNode.level>1){
                $('input[name="en_type"]').val(typeTreeNode.name);
                $('input[name="en_type"]').attr('id',typeTreeNode.id);
                layer.close(layer.index);
            }else{
                layer.msg('请选择工作项！');
            }
        },
        cancel: function(index, layero){
            layer.close(layer.index);
        }
    });
});

//编辑节点
$('#editNode').click(function () {
    if(!window.treeNode||window.treeNode.level==0){
        layer.msg('未选择标段');
        return false;
    }
    whetherShow();
    $('input[type="hidden"][name="add_id"]').val('');
    $('input[type="hidden"][name="edit_id"]').val(window.nodeId);
    $.editNode({
        area:['670px','420px'],
        data:{
            edit_id:window.nodeId
        },
        others:function (res) {
            $('input[name="d_name"]').val(res.d_name);
            $('select[name="type"] option:selected').val(res.type);
            if(res.primary==1){
                $('input[name="primary"]').attr('checked',true);
            }else{
                $('input[name="primary"]').attr('checked',false);
            }
            $('input[name="en_type"]').val(res.en_type_name);
            $('input[name="en_type"]').attr('id',res.en_type);
            $('input[name="d_code"]').val(res.d_code);
            $('textarea[name="remark"]').val(res.remark);
        }
    });
});

//关闭弹层
$.close({
    formId:'nodeForm'
});

//开关
layui.use(['layer', 'form'], function(){
    var form = layui.form;
    form.on('switch(toggle)', function(data){
        if(data.elem.checked==1){
            $('input[name="primary"]').val(1);
        }else{
            $('input[name="primary"]').val(0);
        }
    });
});

//提交节点变更
$('#save').click(function () {
    var add_id = $('input[type="hidden"][name="add_id"]').val();
    var edit_id = $('input[type="hidden"][name="edit_id"]').val();
    var d_code = $('input[name="d_code"]').val();
    var d_name = $('input[name="d_name"]').val();
    var type = $('select[name="type"] option:selected').val();
    var primary = $('input[name="primary"]').val();
    var en_type = $('input[name="en_type"]').attr('id');
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
            section_id:section_id,
            en_type:en_type,
            add_id:add_id,
            edit_id:edit_id
        }
    });
});

//table数据
$.datatable({
    ajax:{
        'url':'/quality/common/datatablesPre?tableName=quality_unit'
    },
    dom: 'lf<".current-path"<"#add.add layui-btn layui-btn-normal layui-btn-sm">>tipr',
    columns:[
        {
            name: "serial_number"
        },
        {
            name: "site"
        },
        {
            name: "coding"
        },
        {
            name: "hinge"
        },
        {
            name: "pile_number"
        },
        {
            name: "start_date"
        },
        {
            name: "completion_date"
        },
        {
            name: "id"
        }
    ],
    columnDefs:[
        {
            "searchable": false,
            "orderable": false,
            "targets": [7],
            "render" :  function(data,type,row) {
                var html = "<i class='fa fa-pencil' uid="+ data +" title='编辑' onclick='edit(this)'></i>" ;
                html += "<i class='fa fa-trash' uid="+ data +" title='删除' onclick='del(this)'></i>" ;
                html += "<i class='fa fa-download' uid="+ data +" title='二维码' onclick='qrcode(this)'></i>" ;
                return html;
            }
        }
    ],
});
$('#add').html('新增');

$('#add').click(function () {
    if(window.treeNode.level<4){
        layer.msg('请选择分项工程');
        return false;
    }
    //系统编码
    var add_id = window.treeNode.add_id;
    $.ajax({
        url: "./getCodeing",
        type: "post",
        data: {
            add_id:add_id
        },
        dataType: "json",
        success: function (res) {
            $('input[name="coding"]').val(res.codeing);
        }
    });
    //单元工程流水号编码
    $('input[name="serial_number_before"]').val(window.treeNode.d_code);
    //新增弹层
    $.add({
        formId:'unit',
        area:['800px','700px'],
        success:function () {
            $('input[name="en_type"]').val('');
        }
    });
});

layui.use('laydate', function(){
    var laydate = layui.laydate;
    laydate.render({
        elem: '#start_date'
    });
    laydate.render({
        elem: '#completion_date'
    });
});

$('.maBasesBtn').click(function () {
    layer.open({
        title:'添加施工依据',
        id:'100',
        type:'1',
        area:['1024px','650px'],
        content:$('#maBasesLayer'),
        btn:['保存'],
        success:function () {
            maBasesTable();
        },
        yes:function () {
            $('input[name="ma_bases"]').val(idArr);
            layer.close(layer.index);
        },
        cancel: function(index, layero){
            layer.close(layer.index);
        }
    });
});

//构建弹层表格
function maBasesTable() {
    $.datatable({
        tableId:'maBasesItem',
        ajax:{
            'url':'/quality/common/datatablesPre?tableName=archive_atlas_cate'
        },
        columns:[
            {
                name: "picture_number"
            },
            {
                name: "picture_name"
            },
            {
                name: "picture_papaer_num"
            },
            {
                name: "a1_picture"
            },
            {
                name: "design_name"
            },
            {
                name: "check_name"
            },
            {
                name: "examination_name"
            },
            {
                name: "completion_date"
            },
            {
                name: "section"
            },
            {
                name: "paper_category"
            },
            {
                name: "id"
            }
        ],
        columnDefs:[
            {
                searchable: false,
                targets:0,
                data:null,
                render:function () {
                    var ipt = "<input type='checkbox' name='checkList' onclick='getSelectId(this)'>";
                    return ipt;
                }
            },
        ],

    });
    //取消全选的事件绑定
    $("thead tr th:first-child").unbind();

    //删除自构建分页位置
    $('#maBasesLayer').show().find('.tbcontainer').remove();

    //翻页事件
    tableItem.on('draw',function () {
        $('input[type="checkbox"][name="checkList"]').prop("checked",false);
        $('#all_checked').prop('checked',false);
        idArr.length=0;
    });
}

//获取选中行ID
var idArr = [];
function getId(that) {
    var isChecked = $(that).prop('checked');
    var id = $(that).parents('tr').find('td:eq(1)').text();
    var checkedLen = $('input[type="checkbox"][name="checkList"]:checked').length;
    var checkboxLen = $('input[type="checkbox"][name="checkList"]').length;
    if(checkedLen===checkboxLen){
        $('#all_checked').prop('checked',true);
    }else{
        $('#all_checked').prop('checked',false);
    }
    if(isChecked){
        idArr.push(id);
        idArr.removalArray();
    }else{
        idArr.remove(id);
        idArr.removalArray();
        $('#all_checked').prop('checked',false);
    }
}

//单选
function getSelectId(that) {
    getId(that);
    console.log(idArr);
}

//checkbox全选
$("#all_checked").on("click", function () {
    var that = $(this);
    if (that.prop("checked") === true) {
        $("input[name='checkList']").prop("checked", that.prop("checked"));
        $('#tableItem tbody tr').addClass('selected');
        $('input[name="checkList"]').each(function(){
            getId(this);
        });
    } else {
        $("input[name='checkList']").prop("checked", false);
        $('#tableItem tbody tr').removeClass('selected');
        $('input[name="checkList"]').each(function(){
            getId(this);
        });
    }
    console.log(idArr);
});

//保存新增
$('#saveUnit').click(function () {
    var tableItem = $('#tableItem').DataTable();
    var serial_number_before = $('input[name="serial_number_before"]').val();
    var serial_number_val = $('input[name="serial_number"]').val();
    var serial_number = serial_number_before + serial_number_val;
    var en_type = $('input[name="en_type"]').attr('id');
    var division_id = window.treeNode.add_id;
    console.log(division_id);
    $.submit({
        tableItem:tableItem,
        tablePath:'/quality/common/datatablesPre?tableName=quality_unit',
        formId:'unit',
        ajaxUrl:'./editUnit',
        data:{
            serial_number:serial_number,
            en_type:en_type,
            division_id:division_id
        },
    });
});
