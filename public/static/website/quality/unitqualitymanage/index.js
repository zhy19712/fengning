//初始化layui组件
var initUi = layui.use('form','laydate');
var form = layui.form;


//单位策划列表
function unitPlanList() {
    $.datatable({
        tableId:'tableItem',
        ajax:{
            'url':'/quality/common/datatablesPre?tableName=unit_quality_control'
        },
        dom: 'ltpr',
        columns:[
            {
                name: "code"
            },{
                name: "name"
            },
            {
                name: "id"
            }
        ],
        columnDefs:[
            {
                "searchable": false,
                "orderable": false,
                "targets": [2],
                "render" :  function(data,type,row) {
                    console.log(data);
                    var html = "<i class='fa fa-download' uid="+ data +" title='下载' onclick='download(this)'></i>" ;
                    html += "<i class='fa fa-print' uid="+ data +" title='打印' onclick='print(this)'></i>" ;
                    html += "<i class='fa fa-times' uid="+ data +" title='删除' onclick='del(this)'></i>" ;
                    return html;
                }
            }
        ],
    });
}

//控制点标准
function controlPointStandard() {
    $.datatable({
        tableId:'controlItem',
        ajax:{
            'url':'/quality/common/datatablesPre?tableName=unit_quality_add_control'
        },
        dom: 'lftipr',
        columns:[
            {
                name: "id",
                "render": function(data, type, full, meta) {
                    var ipt = "<input type='checkbox' name='checkList' idv='"+data+"' onclick='getSelectId(this)'>";
                    return ipt;
                },
            },
            {
                name: "code"
            },{
                name: "name"
            }
        ],
    });
}

/**
 * 导出二维码
 */
//事件
$('#exportQcodeBtn').click(function () {
    var addId = window.treeNode.add_id;
    exportQcode(addId);
});
//方法
function exportQcode(addId) {
    $.ajax({
        url: "./exportCode",
        type: "post",
        data: {
            add_id:addId
        },
        dataType: "json",
        success: function (res) {
            layer.msg(res.msg);
        }
    })
}

/**
 * 添加控制点
 */
//事件
$('#addBtn').click(function () {
    controlPointStandard();
    addControl();
    //取消全选的事件绑定
    $("thead tr th:first-child").unbind();
    $('#tableItem_wrapper,#easyuiLayout').find('.tbcontainer').remove();
})

//方法
function addControl() {
    layer.open({
        title:'控制点选择',
        id:'1',
        type:'1',
        area:['1024px','600px'],
        content:$('#pointLayer'),
        btn:['保存'],
        success:function () {
            $('#pointLayer').css('visibility','initial');
        },
        yes:function () {
            var add_id = $('#enginId').val();
            var ma_division_id = $('#workId').val();
            $.ajax({
                url: "./addControl",
                type: "post",
                data: {
                    add_id:add_id,
                    ma_division_id:ma_division_id,
                    idArr:idArr
                },
                dataType: "json",
                success: function (res) {
                    layer.msg(res.msg);
                    layer.close(layer.index);
                }
            })
        },
        cancel: function(index, layero){
            $('#pointLayer').css('visibility','hidden');
            layer.close(layer.index);
        }
    });
}

//控制点标准
$.ztree({
    treeId:'controlZtree',
    ajaxUrl:'./unitTree',
    zTreeOnClick:function (event, treeId, treeNode){
        $('#controlItem_wrapper,.tbcontainer,#subList').show();
        $.clicknode({
            tableItem:tableItem,
            treeNode:treeNode,
            isLoadTable:true,
            isLoadPath:false,
            parentShow:false,
            tablePath:'/quality/common/datatablesPre?tableName=unit_quality_add_control'
        });
    }
});

//获取选中行ID
var idArr = [];
function getId(that) {
    var isChecked = $(that).prop('checked');
    var id = $(that).attr('idv');
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

//翻页事件
tableItem.on('draw',function () {
    $('input[type="checkbox"][name="checkList"]').prop("checked",false);
    $('#all_checked').prop('checked',false);
    idArr.length=0;
});

//删除
function del(that) {
    var id = $(that).attr('uid');
    delMethod(id);
}

//删除方法
function delMethod(id) {
    var add_id = window.treeNode.add_id;
    var ma_division_id = $('#workId').val();
    $.deleteData({
        ajaxUrl:'./controlDel',
        data: {
            add_id:add_id,
            ma_division_id:ma_division_id,
            id:id
        },
        tablePath:'/quality/common/datatablesPre?tableName=unit_quality_control'
    });
}

$('#delBtn').click(function () {
    delMethod(0);
});


//下载
function download(that) {
    var id = $(that).attr('uid');
    $.download({
        that:that,
        url:'./fileDownload',
        data:{
            id:id
        }
    });
}