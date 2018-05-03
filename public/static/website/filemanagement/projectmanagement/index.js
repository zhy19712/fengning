//
layui.use(['element',"layer",'form','laypage','laydate','layedit'], function(){
  var $ = layui.jquery
    ,element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块

  var form = layui.form
    ,layer = layui.layer
    ,layedit = layui.layedit
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

  //日期选择器
  $(".datepickers").each(function (index,that) {
    laydate.render({
      elem: that
      ,value: new Date() //参数即为：2018-08-20 20:08:08 的时间戳
    });
  });


});

//选中的节点，选中的表格的信息
var sNodes,selectData=""
//初始化表格
var tableItem = $('#tableItem').DataTable( {
  pagingType: "full_numbers",
  processing: true,
  ordering:false,
  serverSide: true,
  ajax: {
    "url":"/filemanagement/common/datatablespre/tableName/file_project_management.shtml"
  },
  dom: 'rtlip',
  columns:[
    {
      name: "directory_code"
    },
    {
      name: "entry_name"
    },
    {
      name: "construction_unit"
    },
    {
      name: "qq"
    },
    {
      name: "vv"
    },
    {
      name: "ww"
    },
    {
      name: "id"
    }
  ],
  columnDefs: [
    {
      "searchable": false,
      "orderable": false,
      "targets": [6],
      "render" :  function(data,type,row) {
        var a = data;
        var html =  "<a type='button' href='javasrcipt:;' class='' style='margin-left: 5px;' onclick='conEdit("+data+")'><i class='fa fa-pencil'></i></a>" ;
        html += "<a type='button' class='' style='margin-left: 5px;' onclick='conDown("+data+")'><i class='fa fa-download'></i></a>" ;
        html += "<a type='button' class='' style='margin-left: 5px;' onclick='conDel("+data+")'><i class='fa fa-trash'></i></a>" ;
        html += "<a type='button' class='' style='margin-left: 5px;' onclick='conPicshow("+data+")'><i class='fa fa-search'></i></a>" ;
        html += "<a type='button' class='' style='margin-left: 5px;' onclick='conPosition("+data+")'><i class='fa fa-gears'></i></a>" ;
        return html;
      }
    }
  ],
  language: {
    "lengthMenu": "_MENU_",
    "zeroRecords": "没有找到记录",
    "info": "第 _PAGE_ 页 ( 共 _PAGES_ 页, _TOTAL_ 项 )",
    "infoEmpty": "无记录",
    "search": "搜索：",
    "infoFiltered": "(从 _MAX_ 条记录过滤)",
    "paginate": {
      "sFirst": "<<",
      "sPrevious": "<",
      "sNext": ">",
      "sLast": ">>"
    }
  },
  "fnInitComplete": function (oSettings, json) {
    $('#tableItem_length').insertBefore(".mark");
    $('#tableItem_info').insertBefore(".mark");
    $('#tableItem_paginate').insertBefore(".mark");
    $('.dataTables_wrapper,.tbcontainer').css("display","block");
  }
});

//获取点击行
$("#tableItem").delegate("tbody tr","click",function (e) {
  if($(e.target).hasClass("dataTables_empty")){
    return;
  }
  $(this).addClass("select-color").siblings().removeClass("select-color");
  selectData = tableItem.row(".select-color").data();//获取选中行数据

  if($(e.target).hasClass("fa-pencil")){
    editbranch($(this).attr("data-tt-id"));
  }
});

//配置
function setcog() {
  layer.open({
    type: 1,
    title: '配置',
    area: ['660px', '488px'],
    content:$("#config"),
    end:function () {

    }
  });
}
//新增
function addbranch() {
  layer.open({
    type: 1,
    title: '配置',
    area: ['100%', '100%'],
    content:$("#memberAdd"),
    success:function (){

    },
    end:function () {

    }
  });
}

//拉取项目类别
function getBranchType() {
  $.ajax({
    url:"./getBranchType",
    type:"GET",
    dataType:"json",
    success:function (res) {
      console.log(res);
      var html = '';
      for (var i = 0 ; i<res.data.length;i++){
        html += '<option value="'+res.data[i]+'">'+res.data[i]+'</option>';
      }
      $("#project_category").append(html);
      layui.form.render('select');
    } 
  })
}
//拉取建设单位
function getGroup() {
  $.ajax({
    url:"./getGroup",
    type:"POST",
    data:{type:1},
    dataType:"json",
    success:function (res) {
      console.log(res);
      var html = '';
      for (var i = 0 ; i<res.data.length;i++){
        html += '<option value="'+res.data[i]+'">'+res.data[i]+'</option>';
      }
      $("#construction_unit").append(html);
      layui.form.render('select');
    }
  })
}

getBranchType();
getGroup();

//获取配置树
function getBranchTree() {
  $.ajax({
    url:'./getBranchTree',
    data:{type:1},
    type:"POST",
    dataType:"JSON",
    success:function (res) {
      console.log(res)
      if(res.code==1){
          console.log(res)
      }else{
        layer.msg(res.msg);
      }
    }
  })
}

//初始化配置树
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
    url : "./getBranchTree",
    dataType :"json"
  },
  data:{
    simpleData : {
      enable:true,
      idkey: "id",
      pIdKey: "pid",
      rootPId:0
    },
    key:{
      name:"class_name"
    }
  },
  check:{
    enable: true,
  }
};
//初始化树
zTreeObj = $.fn.zTree.init($("#ztree"), setting, null);