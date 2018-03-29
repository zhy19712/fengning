    //初始化layui组件
    var initUi = layui.use('form');
    //当前节点id
    var admin_group_id;
    //当前节点名称
    var admin_group_name;
    //管理员分组id
    var admin_cate_id;
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
            onClick:zTreeOnClick,
        },
        showLine:true,
        showTitle:true,
        showIcon:true
    }

    //添加机构
    $('#addOffice').click(function () {
        var treeObj = $.fn.zTree.getZTreeObj("ztree");
        var nodes = treeObj.getSelectedNodes();
        var typeCode;
        var id;

        layer.open({
            id:'5',
            type:'1',
            area:['660px','550px'],
            title:'新增节点',
            btn: ['保存', '关闭'],
            content: $('#addOfficeForm'),
            success:function(){
                $.ajax({
                    url:'./getNode',
                    dataType:'JSON',
                    type:'POST',
                    data:{
                        type:1
                    },
                    success:function(res){
                        var options = [];
                        for(var i = 0;i<res.nodeType.length;i++){
                            options.push('<option value='+ res.nodeType[i].id +'>'+ res.nodeType[i].name +'</option>');
                        }
                        $('select[name="officeType"]').append(options);
                        initUi.form.render();

                        initUi.form.on('select(officeType)', function(data){
                            typeCode = data.value;
                        });
                    }
                });
            },
            yes: function(index, layero){
                if(nodes.length <= 0){
                    id = 0;
                }else{
                    id = nodes[0].id
                }
                var newName = $(layero).find('input[name="officeName"]').val();
                typeCode = $('select[name="officeType"] option:selected').val();
                $.ajax({
                    url:'./editNode',
                    dataType:'JSON',
                    type:'POST',
                    data:{
                        type:typeCode,
                        pid:id,
                        name:newName
                    },
                    success:function(data){
                        $('input[type="hidden"][name="treeId"]').val(data.data);
                        var id = $('input[type="hidden"][name="treeId"]').val();
                        if(nodes.length <= 0){
                            //treeObj.addNodes(null,{name:newName});
                            treeObj.reAsyncChildNodes(null, "refresh",false);
                            //treeObj.expandNode(nodes[0], true, true, true);
                        }else{
                            //treeObj.addNodes(nodes[0], {id:id,pId:nodes[0].pId, name:newName});
                            treeObj.reAsyncChildNodes(null, "refresh",false);
                           //treeObj.expandNode(nodes[0], true, true, true);
                        }
                    }
                });
                $('#addOfficeForm')[0].reset();
                layer.close(index);
            }
        });
        return false;
    });


    //添加节点
    $('#addNode').click(function () {
        //TODO 重构
        var treeObj = $.fn.zTree.getZTreeObj("ztree");
        var nodes = treeObj.getSelectedNodes();
        if(!nodes[0]){
            layer.msg('请选择节点');
            return false;
        }
        $('input[name="pname"]').val(nodes[0].name);

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
                        pid:nodes[0].id,
                        name:newName
                    },
                    success:function(data){
                        $('input[type="hidden"][name="treeId"]').val(data.data);
                        //var id = $('input[type="hidden"][name="treeId"]').val();
                        //treeObj.addNodes(nodes[0], {id:id,pId:nodes[0].pId, name:newName});
                        treeObj.reAsyncChildNodes(null, "refresh",false);
                    }
                });
                $('#addNodeForm')[0].reset();
                layer.close(index);
            }
        });
        return false;
    });

    //编辑节点弹层
    var typeCode;
    function editNode(treeNode, type) {
        $.ajax({
            url:'./getNode',
            dataType:'JSON',
            type:'POST',
            data:{
                type:type,
                id:treeNode.id
            },
            success:function(res){
                $('input[name="name"]').val(res.node.name);
                $('input[name="officeName"]').val(res.node.name);

                $('select[name="officeType"]').html('');

                //构建select
                var options = [];
                for(var i = 0;i<res.nodeType.length;i++){
                    options.push('<option value='+ res.nodeType[i].id +'>'+ res.nodeType[i].name +'</option>');
                }
                $('select[name="officeType"]').append(options);

                //回显select
                $('select[name="officeType"] option').each(function (i,n) {
                    var val = $(n).val();
                    if(res.node.type == val){
                        $(this).attr('selected',true);
                        typeCode = res.node.type;
                    }
                });
                initUi.form.render();

            }
        });
    }

    //提交编辑
    function getEditRes(treeNode,formName,name) {
        initUi.form.on('select(officeType)', function(data){
            typeCode = data.value;
        });
        if(!treeNode.pId){
            treeNode.pId = 0;
        }
        layer.open({
            id:'4',
            type:'1',
            area:['660px','550px'],
            title:'编辑节点',
            btn: ['保存', '关闭'],
            content: $('#'+formName),
            yes: function(index, layero){
                console.log(index, layero);
                var newName = $(layero).find('input[name='+ name +']').val();
                $.ajax({
                    url:'./editNode',
                    dataType:'JSON',
                    type:'POST',
                    data:{
                        type:typeCode,
                        pid:treeNode.pId,
                        id:treeNode.id,
                        name:newName
                    },
                    success:function(data){
                        $('#'+treeNode.tId+'_span').html(newName);
                        layer.msg('编辑成功！', {icon: 1});
                    }
                });
                layer.close(index);
            }
        });
    }

    //编辑节点
    $('#editNode').click(function () {
        var treeObj = $.fn.zTree.getZTreeObj("ztree");
        var nodes = treeObj.getSelectedNodes();
        if (nodes.length > 0) {
            var pNode = nodes[0].getParentNode();
            if(!pNode){
                $('input[name="pname"]').val('上级名称');
            }else{
                $('input[name="pname"]').val(pNode.name);
            }
        }
        if(nodes==''){
            layer.msg('请选择节点');
            return false;
        }
        if(nodes[0].category==1){
            getEditRes(nodes[0],'addOfficeForm','officeName');
        }
        if(nodes[0].category==2){
            getEditRes(nodes[0],'addNodeForm','name');
        }
        editNode(nodes[0],nodes[0].category);
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

    //上移下移方法
    /*function getMoveNode(treeObj,selectNode,treeNode,prevNode,nextNode,state) {
        //TODO 模块做完封装上移下移方法
    }*/

    //上移
    $('#upMoveNode').click(function () {
        var treeObj = $.fn.zTree.getZTreeObj("ztree");
        var selectNode = treeObj.getSelectedNodes();
        var treeNode = selectNode[0];

        if (selectNode.length <= 0){
            layer.msg('请选择节点');
            return false;
        }

        var prevNode = treeNode.getPreNode();

        if (!prevNode){
            layer.msg('已经移到顶啦');
            return false;
        }

        var prevId = prevNode.id;
        var prev_sort_id = prevNode.sort_id;
        var id = treeNode.id;
        var sort_id = treeNode.sort_id;

        $.ajax({
            url: "./sortNode",
            type: "post",
            data: {
                prev_id:prevId,
                prev_sort_id:prev_sort_id,
                id:id,
                id_sort_id:sort_id,
            },
            dataType: "json",
            success: function (res) {
                treeObj.moveNode(prevNode, treeNode, "prev");
                treeObj.reAsyncChildNodes(null, "refresh",false);
            }
        });

    });

    //下移
    $('#downMoveNode').click(function () {
        var treeObj = $.fn.zTree.getZTreeObj("ztree");
        var selectNode = treeObj.getSelectedNodes();
        var treeNode = selectNode[0];

        if (selectNode.length <= 0){
            layer.msg('请选择节点');
            return false;
        }

        var nextNode = treeNode.getNextNode();

        if (!nextNode){
            layer.msg('已经移到底啦');
            return false;
        }

        var nextId = nextNode.id;
        var next_sort_id = nextNode.sort_id;
        var id = treeNode.id;
        var sort_id = treeNode.sort_id;

        $.ajax({
            url: "./sortNode",
            type: "post",
            data: {
                next_id:nextId,
                next_sort_id:next_sort_id,
                id:id,
                id_sort_id:sort_id,
            },
            dataType: "json",
            success: function (res) {
                treeObj.moveNode(nextNode, treeNode, "next");
                treeObj.reAsyncChildNodes(null, "refresh",false);
            }
        });
    });

    //点击节点
    function zTreeOnClick(event, treeId, treeNode) {
        console.log(treeNode);
        $('#groupId').val(treeNode.id);
        admin_group_id = treeNode.id;
        admin_group_name = treeNode.name;
        /*$('input[type="hidden"][name="currentNodeName"]').val(treeNode.name);
        $('input[type="hidden"][name="groupId"]').val(treeNode.id);
        $('input[type="hidden"][name="dataCount"]').val(treeNode.id);*/

        if(treeNode.level==1){
            tableItem.ajax.url("/admin/common/datatablesPre?tableName=admin&id="+ treeNode.id).load();
            console.log(tableItem.rows().count());
        }

        //获取路径
        $.ajax({
            url: "./getParents",
            type: "post",
            data: {id:treeNode.id},
            dataType: "json",
            success: function (res) {
                if(res.msg === "success"){
                    $("#path").text("当前路径:" + res.path)
                }
            }
        });

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
                name: "id"
            }

        ],
        columnDefs: [
            {
                "searchable": false,
                "orderable": false,
                "targets": [7],
                "render" :  function(data,type,row) {
                    var status = row[6];    //后台返回的是否禁用用户状态
                    var html = "<i class='fa fa-search' id="+ data +" title='查看' onclick='view(this)'></i>" ;
                        html += "<i class='fa fa-pencil' id="+ data +" title='编辑' onclick='editor(this)'></i>" ;
                        html += "<i class='fa fa-cog' id="+ data +" title='重置密码' onclick='reset(this)'></i>" ;
                        if(status==1){
                            html += "<i class='fa fa-user-secret' id="+ data +" status="+ status +" title='置为无效' onclick='audit(this)'></i>" ;
                        }else if(status==0){
                            html += "<i class='fa fa-user-times' id="+ data +" status="+ status +" title='置为有效' onclick='audit(this)'></i>" ;
                        }
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

    //新增上传电子签名
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
        $('#signatureId').val(res.id);

        console.log(res.id);
    });

    $('input[name="password"]').keyup(function(){
        var val = $(this).val();
        $('input[name="password_confirm"]').val(val);
        console.log($('input[name="password_confirm"]').val());
    });

    //管理员分组弹层
    $('.manageZtreeBtn').click(function () {
        layer.open({
            id:'6',
            type:'1',
            area:['300px','500px'],
            title:'请选择管理员分组',
            content:$('#manageZtreeDialog')
        });
    });

    //管理员分组树
    var manageSetting = {
        async: {
            enable : true,
            autoParam: ["pId","id"],
            type : "post",
            url : "./getAdminCate",
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
            onDblClick:manageZtreeOnClick
        },
        showLine:true,
        showTitle:true,
        showIcon:true
    }

    //管理员分组树点击
    function manageZtreeOnClick(event, treeId, treeNode) {
        $('input[name="admin_cate_id"]').val(treeNode.name);
        admin_cate_id = treeNode.id;
        layer.close(layer.index);
    }

    $.fn.zTree.init($("#manageZtree"), manageSetting, null);

    //所属部门树弹层
    $('.groupZtreeBtn').click(function () {
        layer.open({
            id:'6',
            type:'1',
            area:['300px','500px'],
            title:'请选择管理员分组',
            content:$('#groupZtreeDialog')
        });
    });

    //所属部门树
    var groupSetting = {
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
            onDblClick:groupZtreeOnClick
        },
        showLine:true,
        showTitle:true,
        showIcon:true
    }

    //所属部门树点击
    function groupZtreeOnClick(event, treeId, treeNode) {
        $('input[name="admin_group_id"]').val(treeNode.name);
        admin_group_id = treeNode.id;
        admin_cate_id = treeNode.id;
        layer.close(layer.index);
    }

    $.fn.zTree.init($("#groupZtree"), groupSetting, null);

    //新增弹层
    $('#add').click(function(){
        if(!admin_group_id){
            layer.msg('请选择节点');
            return false;
        }
        layer.open({
            id:'1',
            type:'1',
            area:['660px','700px'],
            title:'新增',
            content:$('#org'),
            success:function(){
                $('input[name="admin_group_id"]').val(admin_group_name);
            },
            cancel: function(index, layero){
                $('#org')[0].reset();
            }
        });
    });

    //表单提交方法
    function submitForm(data) {
        data.field.admin_group_id = admin_group_id;
        data.field.id = $('input[name="editId"]').val();
        data.field.signature = $('#signatureId').val();
        console.log(data.field);
        $.ajax({
            url:'./publish',
            dataType:'JSON',
            type:'POST',
            data:data.field,
            success:function(res){
                var filter = $(data.elem).attr('lay-filter');
                var groupId = $('#groupId').val();
                if(filter=='save'){
                    layer.closeAll('page');
                }
                $('#org')[0].reset();
                layer.msg(res.msg);
                tableItem.ajax.url("/admin/common/datatablesPre?tableName=admin&id="+ groupId).load();
            }
        });
    }

    //表单提交
    layui.use('form',function(){
        var form = layui.form;
        //表单提交
        form.on('submit(save)', function(data){
            console.log(data);
            submitForm(data);
            return false;
        });
        form.on('submit(saveAndCreat)', function(data){
            submitForm(data);
            return false;
        });
    });

    //关闭弹层
    $('.close').click(function () {
        $('#org')[0].reset();
        layer.closeAll('page');
    });

    //查看表单
    function viewForm(id){
        $.ajax({
            url:'./publish',
            dataType:'JSON',
            type:'GET',
            data:{
                type:'group',
                id:id
            },
            success:function(res){
                var data = res.admin;
                $('input[name="nickname"]').val(data.nickname);
                $('input[name="order"]').val(data.order);
                $('input[name="admin_group_id"]').val(admin_group_name);
                $('input[name="admin_cate_id"]').val(data.admin_cate_id);
                $('input[name="mail"]').val(data.mail);
                $('input[name="mobile"]').val(data.mobile);
                $('input[name="tele"]').val(data.tele);
                $('input[name="name"]').val(data.name);
                $('input[name="position"]').val(data.position);
                $('input[name="wechat"]').val(data.wechat);
                $('select[name="gender"]').val(data.gender);
                $('input[name="signature"]').val(data.signature);
                $('textarea[name="remark"]').val(data.remark);
            }
        });
    }

    //查看弹层
    function view(that) {
        var id = $(that).attr('id');
        layer.open({
            id:'2',
            type:'1',
            area:['700px','600px'],
            title:'查看',
            btn:['关闭'],
            content:$('#orgStatic'),
            success:function () {
                viewForm(id);
            },
            yes:function () {
                $('#org')[0].reset();
                layer.closeAll('page');
            },
            cancel: function(index, layero){
                $('#org')[0].reset();
            }
        });
    }

    //编辑弹层
    function editor(that) {
        var id = $(that).attr('id');
        $('input[name="editId"]').val(id);
        layer.open({
            id:'1',
            type:'1',
            area:['660px','700px'],
            title:'编辑',
            content:$('#editOrg'),
            success:function(){
                viewForm(id);
            },
            cancel: function(index, layero){
                $('#org')[0].reset();
            }
        });
    }

    //重置密码
    function reset(that) {
        var id = $(that).attr('id');
        $.ajax({
            url:'./editPwd',
            dataType:'JSON',
            type:'POST',
            data:{
                id:id
            },
            success:function(res){
                layer.msg(res.msg);
            }
        });
    }

    //用户禁用/解禁
    function audit(that) {
        var status = $(that).attr('status');
        var id = $(that).attr('id');
        $.ajax({
            url:'./audit',
            dataType:'JSON',
            type:'POST',
            data:{
                id:id
            },
            success:function(res){
                if(res.code==0){
                    layer.msg(res.msg);
                    return false;
                }
                if(status==1){
                    $(that).attr('status','0');
                    $(that).addClass('fa-user-times').removeClass('fa-user-secret');
                }else if(status==0){
                    $(that).attr('status','1');
                    $(that).addClass('fa-user-secret').removeClass('fa-user-times');
                }
                layer.msg(res.msg);
            }
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