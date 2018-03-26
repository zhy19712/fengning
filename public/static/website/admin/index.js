$(function(){
    //组织结构树
    var setting = {
        async: {
            enable : true,
            autoParam: ["pId"],
            type : "post",
            url : "./index",
            dataType :"json"
        },
        dit:{
            enable:true,
            drag:{
                isMove: true
            }
        },
        showLine:true,
        showTitle:true,
        showIcon:true
    }

    //添加节点
    function addNode(treeNode) {
        if(!treeNode){
            layer.msg('请选择节点');
            return false;
        }
        var zTree = $.fn.zTree.getZTreeObj("ztree");
        layui.use('layer', function(){
            var layer = layui.layer;
            //将新节点添加到数据库中
            layer.prompt({
                title: '请输入节点名称',
            },function(value, index, elem){
                $.ajax({
                    url:'{:url("Admin/editNode")}',
                    dataType:'JSON',
                    type:'POST',
                    data:{
                        pid:treeNode.id,
                        pname:value
                    },
                    success:function(data){
                        $('input[type="hidden"][name="treeId"]').val(data.data);
                        var id = $('input[type="hidden"][name="treeId"]').val();
                        zTree.addNodes(treeNode, {id:id,pId:treeNode.pId, name:newName});
                    }
                });
                layer.close(index);
            });
        });

        return false;
    };

    //添加节点事件
    $('#addNode').click(function () {
        var treeObj = $.fn.zTree.getZTreeObj("ztree");
        var nodes = treeObj.getSelectedNodes();
        console.log(nodes[0]);
        addNode(nodes[0]);
    });

    //编辑节点
    function editNode(treeNode, newName) {
        $.ajax({
            url:'{:url("Statutestdi/nodeAdd")}',
            dataType:'JSON',
            type:'POST',
            data:{
                id:treeNode.id,
                pname:newName
            },
            success:function(){
                $('#'+treeNode.tId+'_span').html(newName);
                layer.msg('编辑成功！', {icon: 1});
            }
        });
    }

    $('#editNode').click(function () {
        var treeObj = $.fn.zTree.getZTreeObj("ztree");
        var nodes = treeObj.getSelectedNodes();
        layui.use('layer', function(){
            var layer = layui.layer;
            if(nodes==''){
                layer.msg('请选择节点');
                return false;
            }
            layer.prompt({
                title: '编辑',
            },function(value, index, elem){
                if(!value){
                    layer.msg('请输入节点名称');
                    return false;
                }
                editNode(nodes[0],value);
                layer.close(index);
            });
        });
    })


    zTreeObj = $.fn.zTree.init($("#ztree"), setting, null);

    //组织结构表格

    /*var tableItem = $('#tableItem').DataTable( {
        processing: true,
        serverSide: true,
        ajax: {
            "url":"{:url('common/datatablesPre?tableName=admin')}"
        },
        /!*dom: 'lf<"#manageExportExcel.export-excel btn-outline btn-primary"><"#manageExport.export btn-outline btn-primary"><"#manageImport.import btn-outline btn-primary"><"#manageAdd.add btn-outline btn-primary">rtip',*!/
        columns: [
            {
                name: "order"
            },
            {
                name: "name"
            },
            {
                name: "nickname"
            },
            {
                name: "name"
            },
            {
                name: "mobile"
            },
            {
                name: "position"
            },
            {
                name: "status"
            }
        ],
        columnDefs: [
            {
                "searchable": false,
                "orderable": false,
                "targets": [7],
                "render" :  function(data,type,row) {
                    var html = "<button type='button' class='' style='margin-left: 5px;' onclick='manageEdit(this)'><i class='fa fa-search'></i></button >" ;
                    html += "<button type='button' class='' style='margin-left: 5px;' onclick='manageDel(this)'><i class='fa fa-pencil'></i></button>" ;
                    html += "<button type='button' class='' style='margin-left: 5px;' onclick='manageDel(this)'><i class='fa fa-cog'></i></button>" ;
                    html += "<button type='button' class='' style='margin-left: 5px;' onclick='manageDel(this)'><i class='fa fa-user-secret'></i></button>" ;
                    return html;
                }
            }
        ],
        language: {
            "sProcessing":"数据加载中...",
            "lengthMenu": "每页_MENU_ 条记录",
            "zeroRecords": "没有找到记录",
            "info": "第 _PAGE_ 页 ( 总共 _PAGES_ 页 )",
            "infoEmpty": "无记录",
            "search": "搜索：",
            "infoFiltered": "(从 _MAX_ 条记录过滤)",
            "paginate": {
                "previous": "上一页",
                "next": "下一页"
            }
        }
    });*/
})