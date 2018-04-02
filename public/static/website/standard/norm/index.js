//初始化layui组件
var initUi = layui.use('form','laydate');
//工程标准及规范树
$.ztree();
//点击节点
function zTreeOnClick(event, treeId, treeNode){
    $.clicknode({
        treeNode:treeNode,
        tablePath:'/standard/common/datatablesPre?tableName=norm_file',
        isLoadPath:false
    });
}
//工程标准及规范表格
$.datatable({
    ajax:{
        'url':'/standard/common/datatablesPre?tableName=norm_file'
    },
    dom: 'lf<".current-path"<"#add.add layui-btn layui-btn-normal layui-btn-sm"<".fa">>>tipr',
    columns:[
        {
            name: "standard_number"
        },
        {
            name: "standard_name"
        },
        {
            name: "material_date"
        },
        {
            name: "alternate_standard"
        },
        {
            name: "remark"
        },
        {
            name: "id"
        }
    ],
    columnDefs:[
        {
            "searchable": false,
            "orderable": false,
            "targets": [5],
            "render" :  function(data,type,row) {
                var html = "<i class='fa fa-search' uid="+ data +" title='下载' onclick='download(this)'></i>" ;
                return html;
            }
        }
    ],
});
$('#add').html('新增');
//新增弹层
$.add({
    area:['660px','400px']
});
//关闭弹层
$.close();
//表单提交
$('#save').click(function () {
    $.submit({
        ajaxUrl:'./editNode',
        data:{
            file_id : window.file_id
        },
        tablePath:'/standard/common/datatablesPre?tableName=norm_file'
    });
});

//日期
layui.use('laydate', function(){
    var laydate = layui.laydate;
    laydate.render({
        elem: '#material_date'
    });
});
//上传
uploader = WebUploader.create({
    auto: true,
    // swf文件路径
    swf:  '/static/public/webupload/Uploader.swf',

    // 文件接收服务端。
    server: "/standard/common/upload",

    // 选择文件的按钮。可选。
    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
    pick: {
        multiple: false,
        id: "#upload",
        innerHTML: "上传"
    },
    formData:{
        module:'norm',
        use:'norm_file',
    },
    // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
    resize: false
});

uploader.on( 'uploadSuccess', function( file ,res) {
    layer.msg(file.name+'已上传成功');
    $('input[name="file_name"]').val(file.name);
    window.file_id = res.id;
});
//下载
function download(that) {
    $.download({
        that:that,
        url:'./fileDownload',
        submitPath:'./fileDownload'
    });
}