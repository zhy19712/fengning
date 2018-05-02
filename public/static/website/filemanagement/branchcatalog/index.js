//layui
layui.use(['element',"layer",'form','upload'], function(){
  var $ = layui.jquery
    ,element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块

  var form = layui.form
    ,layer = layui.layer
    ,layedit = layui.layedit
    ,upload = layui.upload
    ,laydate = layui.laydate;

  //监听提交
  form.on('submit(demo1)', function(data){
    $.ajax({
      type: "post",
      url:"./editCate",
      data:data.field,
      success: function (res) {
        console.log(selfid)
        if(res.code == 1) {

          getTable(selfid);
          layer.closeAll();
        }else{
          layer.msg(res.msg);
        }
      }
    });
    return false;
  });
  form.on('submit(demo2)', function(data){
    $.ajax({
      type: "post",
      url:"./importExcel",
      data:data.field,
      success: function (res) {
        console.log(selfid)
        if(res.code == 1) {
          getTable(selfid);
          layer.closeAll();
        }else{
          layer.msg(res.msg);
        }
      }
    });
    return false;
  });
  //上传
  upload.render({
    elem: '#upload',
    url: "../common/upload/module/file/use/file_thumb.shtml",
    accept: 'file',//普通文件
    size:89000,
    before: function(obj){
      obj.preview(function(index, file, result){
        uploadName = file.name;
        $("#file_name_1").val(file.name);
      })
    },
    done:function (res) {
      if(res.code!=2){
        layer.msg("上传失败");
        return ;
      }
      $("#file_name").val(res.filename);
      $("#attachmentId").val(res.id);
    }
  });
});
//
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
//组织结构树
var setting = {
  view: {
    showLine: true, //设置 zTree 是否显示节点之间的连线。
    selectedMulti: false, //设置是否允许同时选中多个节点。
    // dblClickExpand: true //双击节点时，是否自动展开父节点的标识。
  },
  async: {
    enable : true,
    autoParam: ["pid","id"],
    type : "post",
    url : "./projecttree",
    dataType :"json",
    dataFilter:ajaxDataFilter
  },
  data:{
    simpleData : {
      enable:true,
      idkey: "id",
      pIdKey: "pid",
      rootPId:0
    }
  },
  callback: {
    onClick: this.onClick
  }
};

//初始化树
zTreeObj = $.fn.zTree.init($("#ztree"), setting, null);

//选中的节点id，ztree对象，父节点id，选中的节点，选中的表格的信息
var selfid,zTreeObj,groupid,sNodes,selectData="";

//点击选中的树节点
function onClick(e, treeId, node) {
  sNodes = zTreeObj.getSelectedNodes();//选中节点
  selfid = zTreeObj.getSelectedNodes()[0].id;
  node = sNodes[0].getParentNode();//获取父节点
  groupid = sNodes[0].pid //父节点的id
  getTable(selfid);
}

//点击添加或编辑节点
function editNode(selfid,pid) {
  layer.prompt({
    title: '请输入节点名称',
    value:selfid===""?"":sNodes[0].name
  },function(value, index, elem){
    $.ajax({
      url:'./editNode',
      type:"post",
      data:{pid:pid,name:value,id:selfid},
      success: function (res) {
        if(res.code===1){
          if(pid !== "") {
            zTreeObj.addNodes(sNodes[0], res.data);
          }else{
            sNodes[0].name = value;
            zTreeObj.updateNode(sNodes[0]);//更新节点名称
            layer.msg("编辑成功")
          }

        }else{
          layer.msg(res.msg);
        }
      }
    });
    layer.close(index);
  });

};

//改变节点
function changeNodeTree(action){
  if(!selfid){
    layer.msg("请选择节点");
    return;
  }
  if(selfid==1 && (action == "del"||action == "edit")){
    layer.msg("系统节点不允许操作！", { icon: 2 });
    return;
  }
  //删除
  if (action == "del" ){
    if(!sNodes[0].children){
        $.ajax({
          url:'./delNode',
          type:"post",
          data:{id:selfid},
          success: function (res) {
            if(res.code===1){
              layer.msg("删除节点成功",{time:1500,shade: 0.1});
              // var url = "/admin/common/datatablespre/tableName/admin_cate/id/"+selfid+".shtml";
              // tableItem.ajax.url(url).load();
              zTreeObj.removeNode(sNodes[0]);
              // selfid = "";
            }else{
              layer.msg(res.msg, { icon: 2 });
            }
          }
        });
    }else{
      layer.msg("包含下级，无法删除",{time:1500,shade: 0.1});
    }
  }else if(action == "add"){
    editNode("",selfid);
  }else{
    editNode(selfid,'');
  }
}

//全部展开
$('#openNode').click(function(){
  zTreeObj.expandAll(true);
});

//收起所有
$('#closeNode').click(function(){
  zTreeObj.expandAll(false);
});

//添加分支
function addbranch(){
  $("#branchform input").val('');
  $("#branchform #classifyid").val(selfid);
  if(!selfid || selfid == 1){
    layer.msg("请先选择正确的分类！");
    return;
  };
  layer.open({
    type: 1,
    title: '编辑',
    area: ['690px', '340px'],
    content:$("#branchform")
  });
  if(selectData!=""){
    $("#parent_code").val(selectData[1]);
    $("#pid").val(selectData[3]);
  }
}
//编辑
function editbranch(id){
  layer.open({
    type: 1,
    title: '编辑',
    area: ['690px', '340px'],
    content:$("#branchform"),
    end:function () {
      selectData = "";
    }
  });
  $("#parent_code").val(selectData[0]);
  $("#code").val(selectData[1]);
  $("#class_name").val(selectData[2]);
  $("#branchform #classifyid").val(selfid);
  $("#branchform #addId").val(id);
}

//删除
function conDel(id){
  $.ajax({
    type:"POST",
    url:"./delCate",
    data:{id:id},
    dataType:"JSON",
    success:function (res) {
      if(res.code==1){
        layer.msg("删除成功");
        getTable(selfid);
      }else{
        layer.msg(res.msg)
      }
    }
  })
}


// 拉取表格的数据
function getTable(id){
  $("#tableItem tbody").empty();
  $.ajax({
    type:"POST",
    url:"./table",
    data:{id:id},
    dataType:"JSON",
    success:function (res) {
      var html = ""
      for (var i=0;i<res.length;i++){
        var parent_code = Object.is(res[i].parent_code,null)?"":res[i].parent_code;
        html += '<tr data-tt-id="'+res[i].id+'" data-tt-parent-id="'+res[i].pid+'" data-parent-code="'+parent_code+'">' +
          '<td >'+res[i].code+'</td>' +
          '<td >'+res[i].class_name+'</td>' +
          '<td ><a type="button" class="" style="margin-left: 5px;"><i class="fa fa-pencil"></i></a><a type="button" class="" style="margin-left: 5px;" onclick="conDel('+res[i].id+')"><i class="fa fa-trash"></i></a></td>' +
          '</tr>';
      }
      $("#tableItem tbody").append($(html));

      $("#tableItem").treetable({ expandable: true, initialState :"expanded" },true);
    }
  })
}

$("#tableItem").treetable({ expandable: true, initialState :"expanded" }).show();

//获取点击行
$("#tableItem").delegate("tbody tr","click",function (e) {
  if($(e.target).hasClass("dataTables_empty")){
    return;
  }
  $(this).addClass("select-color").siblings().removeClass("select-color");
  selectData = [$(this).attr("data-parent-code"),$(this).find("td:first-child").text().trim(),$(this).find("td:nth-child(2)").html().trim(),$(this).attr("data-tt-id")];

  if($(e.target).hasClass("fa-pencil")){
    editbranch($(this).attr("data-tt-id"));
  }
});

//导入
function importExcel() {
  $("#excel input").val('');
  $("#excel #classifyid_2").val(selfid);
  layer.open({
    type: 1,
    title: '编辑',
    area: ['690px', '340px'],
    content:$("#excel")
  });
}
//下载模板
function download() {
  $.ajax({
    url: "./excelDownload",
    type:"GET",
    dataType: "json",
    success: function (res) {
      if(res.code != 1){
        layer.msg(res.msg);
      }else {
        $("#form_container").empty();
        var str = "";
        str += ""
          + "<iframe name=downloadFrame style='display:none;'></iframe>"
          + "<form name=download action='./excelDownload' method='get' target=downloadFrame>"
          + "<span class='file_name' style='color: #000;'>"+str+"</span>"
          + "<input class='file_url' style='display: none;' name='id'>"
          + "<button type='submit' class=btn></button>"
          + "</form>"
        $("#form_container").append(str);
        $("#form_container").find(".btn").click();
      }
    }
  })
}