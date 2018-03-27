    //初始化layui组件

    //组织结构树
    var setting = {
        async: {
            enable : true,
            autoParam: ["pId","id"],
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
        data: {
            simpleData: {
                enable: true,
                idKey: "id",
                pIdKey: "pId"
            }
        },
        view:{
            selectedMulti: false
        },
        callback:{
            onClick:zTreeOnClick
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
        //将新节点添加到数据库中
        layer.open({
            id:'3',
            type:'1',
            area:['660px','200px'],
            title:'新增节点',
            btn: ['保存', '关闭'],
            content: $('#addNodeForm'),
            yes: function(index, layero){
                console.log(index, layero);
                var newName = $(layero).find('input[name="name"]').val();
                $.ajax({
                    url:'./editNode',
                    dataType:'JSON',
                    type:'POST',
                    data:{
                        pid:treeNode.id,
                        name:newName
                    },
                    success:function(data){
                        $('input[type="hidden"][name="treeId"]').val(data.data);
                        var id = $('input[type="hidden"][name="treeId"]').val();
                        zTree.addNodes(treeNode, {id:id,pId:treeNode.pId, name:newName});
                    }
                });
                layer.close(index);
            }
        });
        return false;
    };

    //添加节点事件
    $('#addNode').click(function () {
        var treeObj = $.fn.zTree.getZTreeObj("ztree");
        var nodes = treeObj.getSelectedNodes();
        $('input[name="pname"]').val(nodes[0].name);
        console.log(nodes[0]);
        addNode(nodes[0]);
    });

    //编辑节点弹层
    function editNode(treeNode, newName) {
        $.ajax({
            url:'./getNode',
            dataType:'JSON',
            type:'POST',
            data:{
                id:treeNode.id
            },
            success:function(res){
                $('input[name="name"]').val(res.node.name);
                console.log(res);
            }
        });
    }

    //编辑节点
    $('#editNode').click(function () {
        var treeObj = $.fn.zTree.getZTreeObj("ztree");
        var nodes = treeObj.getSelectedNodes();
        if (nodes.length > 0) {
            var pNode = nodes[0].getParentNode();
            $('input[name="pname"]').val(pNode.name);
        }
        if(nodes==''){
            layer.msg('请选择节点');
            return false;
        }
        editNode(nodes[0]);
        layer.open({
            id:'4',
            type:'1',
            area:['660px','200px'],
            title:'编辑节点',
            btn: ['保存', '关闭'],
            content: $('#addNodeForm'),
            yes: function(index, layero){
                console.log(index, layero);
                var newName = $(layero).find('input[name="name"]').val();
                $.ajax({
                    url:'./editNode',
                    dataType:'JSON',
                    type:'POST',
                    data:{
                        pid:nodes[0].pId,
                        id:nodes[0].id,
                        name:newName
                    },
                    success:function(data){
                        $('#'+nodes[0].tId+'_span').html(newName);
                        layer.msg('编辑成功！', {icon: 1});
                    }
                });
                layer.close(index);
            }
        });
    });

    //删除节点
    $('#delNode').click(function () {
        var treeObj = $.fn.zTree.getZTreeObj("ztree");
        var nodes = treeObj.getSelectedNodes();
        var isParent;
        if(nodes==''){
            layer.msg('请选择节点');
            return false;
        }
        //是否选择节点
        if (nodes.length > 0) {
            isParent = nodes[0].isParent;
        }

        //禁止删除父节点
        if(isParent){
            layer.msg('包含下级，无法删除。');
            return false
        }

        layer.confirm('该操作会将关联用户同步删除，是否确认删除？',{
            icon:3,
            title:'提示'
        },function(index){
            $.ajax({
                url:'./delNode',
                dataType:'JSON',
                type:'POST',
                data:{
                    id:nodes[0].id
                },
                success:function(){
                    for (var i=0, l=nodes.length; i < l; i++){
                        treeObj.removeNode(nodes[i]);
                    }
                    layer.msg('删除成功！', {icon: 1});
                }
            });
            layer.close(index);
        });
    });

    //全部展开
    $('#openNode').click(function(){
        var treeObj = $.fn.zTree.getZTreeObj("ztree");
        treeObj.expandAll(true);
    });

    //收起所有
    $('#closeNode').click(function(){
        var treeObj = $.fn.zTree.getZTreeObj("ztree");
        treeObj.expandAll(false);
    });

    //上移
    $('#upMoveNode').click(function () {
        var treeObj = $.fn.zTree.getZTreeObj("ztree");
        var selectNode = treeObj.getSelectedNodes();

        if (selectNode.length <= 0){
            layer.msg('请选择节点');
            return false;
        }

        var node = selectNode[0].getPreNode();
        if (node===null){
            layer.msg('已经移到顶啦');
            return false;
        }
        treeObj.moveNode(node, selectNode[0], "prev");
    });

    //下移
    $('#downMoveNode').click(function () {
        var treeObj = $.fn.zTree.getZTreeObj("ztree");
        var selectNode = treeObj.getSelectedNodes();

        if (selectNode.length <= 0){
            layer.msg('请选择节点');
            return false;
        }

        var node = selectNode[0].getNextNode();
        if (node===null){
            layer.msg('已经移到底啦');
            return false;
        }
        treeObj.moveNode(node, selectNode[0], "next");
    });

    //点击节点
    function zTreeOnClick(event, treeId, treeNode) {
        console.log(treeNode);
        /*$('input[type="hidden"][name="currentNodeName"]').val(treeNode.name);
        $('input[type="hidden"][name="groupId"]').val(treeNode.id);
        $('input[type="hidden"][name="dataCount"]').val(treeNode.id);*/

        if(treeNode.isParent){
           // $('#tableItem_wrapper').css("visibility","hidden");
        }else{
            //$('#tableItem_wrapper').css("visibility","visible");
            tableItem.ajax.url("/admin/common/datatablesPre?tableName=admin&id="+ treeNode.id).load();
            console.log(tableItem.rows().count());
        }

        //获取路径
        /*$.ajax({
            url: "./getParents",
            type: "post",
            data: {id:treeNode.id},
            dataType: "json",
            success: function (res) {
                if(res.msg === "success"){
                    $("#path").text("当前路径:" + res.path)
                }
            }
        });*/

        //构建按版本查询
        //viewHistoryVer(treeNode.id);

    };

    zTreeObj = $.fn.zTree.init($("#ztree"), setting, null);

    //组织结构表格

    var tableItem = $('#tableItem').DataTable( {
        processing: true,
        serverSide: true,
       /* data : [
            ['Trident','Internet Explorer 4.0','Win 95+','4','X','Y','Z'],
        ],*/
        ajax: {
            "url":"/admin/common/datatablesPre?tableName=admin"
        },
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
                    var html = "<i class='fa fa-search' title='查看' onclick='abcd(this)'></i>" ;
                        html += "<i class='fa fa-pencil' title='编辑' onclick='weEdit(this)'></i>" ;
                        html += "<i class='fa fa-cog' title='重置密码' onclick='reset(this)'></i>" ;
                        html += "<i class='fa fa-user-secret' title='置为有效' onclick='active(this)'></i>" ;
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
    });

    //上传电子签名
    uploader = WebUploader.create({
        auto: true,
        // swf文件路径
        swf:  '/static/public/webupload/Uploader.swf',

        // 文件接收服务端。
        server: "/admin/common/upload",

        // 选择文件的按钮。可选。
        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
        pick: {
            multiple: false,
            id: "#upload",
            innerHTML: "上传"
        },
        formData:{
            major_key:'',
            group_id:'',
            number:'',
            go_date:'',
            standard:'',
            evaluation:'',
            sdi_user:'',
        },
        accept: {
            title: 'Images',
            extensions: 'gif,jpg,jpeg,bmp,png',
            mimeTypes: 'image/jpg,image/jpeg,image/png'
        },
        // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
        resize: false

    });

    $('.webuploader-pick').next().css({
        width:'58px',
        height:'39px'
    });

    uploader.on( 'uploadSuccess', function( file ,res) {
        layer.msg(file.name+'已上传成功');
        $('input[name="signature"]').val(file.name);
        console.log(res.id);
    });

    $('input[name="password"]').keyup(function(){
        var val = $(this).val();
        $('input[name="password_confirm"]').val(val);
        console.log($('input[name="password_confirm"]').val());
    })

    //新增弹层
    $('#add').click(function(){
        var addIndex = layer.open({
            id:'1',
            type:'1',
            area:['660px','600px'],
            title:'新增',
            content:$('#org')
        });
    });
    layui.use('form',function(){
        var form = layui.form;
        //表单提交
        form.on('submit(saveAndCreat)', function(data){
            data.field.pid =
            $.ajax({
                url:'./publish',
                dataType:'JSON',
                type:'POST',
                data:data,
                success:function(){
                    layer.msg('保存成功');
                }
            });
        });
    });

    //关闭弹层
    $('#close').click(function () {
        layer.closeAll('page')
    })


    //查看
    function abcd() {
        layer.open({
            id:'2',
            type:'1',
            area:['650px','600px'],
            title:'新增',
            content:$('#org')
        });
    }

    //数据返回
    /*function complete(data){
        var groupid = $('input[type="hidden"][name="groupId"]').val();
        console.log(data);
        if(data.code==1){
            layer.msg(data.msg);
            tableItem.ajax.url("__SCRIPT__/safety_statutesdi.php?pid=" + groupid).load();
            importExcel.reset();
            viewHistoryVer(groupid);
            $("#addRules_modal").modal('hide');

        }else{
            layer.msg(data.info,{icon: 5});
            importExcel.reset();
            return false;
        }
    }*/