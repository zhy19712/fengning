//
layui.use(['element',"layer",'form','laypage','laydate'], function(){
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
});

//选中的节点，选中的表格的信息
var sNodes,selectData=""
//初始化表格
var tableItem = $('#tableItem').DataTable( {
  pagingType: "full_numbers",
  processing: true,
  ordering:false,
  // serverSide: true,
  // ajax: {
  //   "url":"/quality/common/datatablespre/tableName/"+tableName+".shtml"
  // },
  dom: 'rtlip',
  // columns:[
  //   {
  //     name: "filename"
  //   },
  //   {
  //     name: "date"
  //   },
  //   {
  //     name: "owner"
  //   },
  //   {
  //     name: "company"
  //   },
  //   {
  //     name: "position"
  //   },
  //   {
  //     name: "id"
  //   }
  // ],
  // columnDefs: [
  //   {
  //     "searchable": false,
  //     "orderable": false,
  //     "targets": [5],
  //     "render" :  function(data,type,row) {
  //       var a = data;
  //       var html =  "<a type='button' href='javasrcipt:;' class='' style='margin-left: 5px;' onclick='conEdit("+data+")'><i class='fa fa-pencil'></i></a>" ;
  //       html += "<a type='button' class='' style='margin-left: 5px;' onclick='conDown("+data+")'><i class='fa fa-download'></i></a>" ;
  //       html += "<a type='button' class='' style='margin-left: 5px;' onclick='conDel("+data+")'><i class='fa fa-trash'></i></a>" ;
  //       html += "<a type='button' class='' style='margin-left: 5px;' onclick='conPicshow("+data+")'><i class='fa fa-search'></i></a>" ;
  //       html += "<a type='button' class='' style='margin-left: 5px;' onclick='conPosition("+data+")'><i class='fa fa-gears'></i></a>" ;
  //       return html;
  //     }
  //   },
  //   {
  //     "orderable": false,
  //     "targets": [4],
  //     "render":function (data) {
  //       if(data==0||!data){
  //         return "" ;
  //       }else{
  //         return "<img src='/static/webSite/quality/scenepicture/setValid.png'>" ;
  //       }
  //     }
  //   }
  // ],
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
    area: ['660px', '388px'],
    content:$("#config"),
    end:function () {
      selectData = "";
      $("#branchform input").val('');
    }
  });
}