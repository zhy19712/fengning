<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/4/28
 * Time: 10:48
 */
/**
 * 档案管理-待整理文件
 * Class Awaitfile
 * @package app\filemanagement\controller
 */
namespace app\filemanagement\controller;
use app\admin\controller\Permissions;
use app\filemanagement\model\ProjectmanagementModel;//档案管理-工程项目管理
use app\filemanagement\model\FilebranchtypeModel;//档案管理-分支目录管理-项目分类-树节点
use app\filemanagement\model\FilebranchModel;//档案管理-分支目录管理-项目分类
use app\filemanagement\model\FileelectronicModel;//档案管理-档案管理-待整理文件-电子文件挂接
use app\filemanagement\model\Filependingdocuments;//档案管理-档案管理-待整理文件
use app\archive\model\DocumentTypeModel;//文档管理类型
use app\archive\model\DocumentModel;//文档管理
use app\quality\model\DivisionModel;//工程划分，单元工程
use app\quality\model\DivisionUnitModel;//单元工程
use app\quality\model\UploadModel;//单元工程文件上传
use app\standard\model\MaterialTrackingDivision;//工序表
use think\exception\PDOException;
use think\Loader;
use think\Db;
use think\Request;
use think\Session;

class Awaitfile extends Permissions
{
    /**
     * 待整理文件模板首页
     */
    public function index()
    {
        return $this->fetch();
    }

    /**********************************左侧的目录树*********************/
    /**
     * 获取所有的工程项目管理中的项目名称
     * @return \think\response\Json
     */
    public function getAllCategory()
    {
        //实例化模型类
        $model = new ProjectmanagementModel();
        $category = $model->getAllCategory();
        //定义一个空数组
        if(!empty($category))
        {
            $data = $category;
        }else
        {
            $data = [];
        }
        return json(["code"=>1,"data"=>$data]);
    }

    /**
     * 获取所选的项目名称下的树
     * @return \think\response\Json
     */
    public function getCategoryTree()
    {
        if(request()->isAjax()){
            //实例化模型类
            $model = new ProjectmanagementModel();
            $branch_model = new FilebranchModel();
            $id = input("post.id");
            $id = 1;
            //查询当前的所选的项目名称下的所有目录
            $branch_info = $model->getOne($id);
            $branch_id_list = $branch_info["branch_id"];
            if(!empty($branch_id_list))
            {
                $branch_id_array = explode(",",$branch_id_list);

            }else
            {
                $branch_id_array = [];
            }

            //定义一个空的数组
            $branch_array = array();
            //遍历配置的项目名称数组id,查询分支目录管理-项目分类
            foreach($branch_id_array as $key=>$val)
            {
                $branch_data_info = $branch_model->getOne($val);
                $branch_array[$key]["id"] = $branch_data_info["id"];
                $branch_array[$key]["classifyid"] = $branch_data_info["classifyid"];
                $branch_array[$key]["pid"] = $branch_data_info["pid"];
                $branch_array[$key]["class_name"] = $branch_data_info["class_name"];
            }
            if(!empty($branch_array))
            {
                $result = tree($branch_array);
                foreach ((array)$result as $k => $v) {
                    $v['id'] = strval($v['id']);
                }
            }else
            {
                $result = [];
            }
            return json(["code"=>1,"data"=>$result]);
        }
    }

    /**********************************电子文件挂接选择文件目录树*********************/
    /**
     * 文档管理树
     * @return \think\response\Json
     */
    public function getDocumenttypeTree()
    {
        if(request()->isAjax()) {
            //实例化模型类
            $model = new DocumentTypeModel();
            $data = $model->getall();
            $res = tree($data);
            foreach ((array)$res as $k => $v) {
                $v['id'] = strval($v['id']);
            }
            return json($res);
        }
    }

    /**
     * 工程划分，单元工程管理树
     * @return \think\response\Json
     */
    public function getDivisionTree()
    {
        if(request()->isAjax()){
            $node = new DivisionModel();
            $nodeStr = $node->getNodeInfo();
            return json($nodeStr);
        }
    }

    /**
     * 获取检验批列表
     * @param $id
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getDivisionUnitTree()
    {
        if(request()->isAjax()) {
            $id = input("post.id");
//            $id = 4;
            return json(DivisionUnitModel::all(['division_id' => $id]));
        }
    }

    /**
     * 获取工序列表
     * @param $id
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getProcedures()
    {
        if(request()->isAjax()) {
            $id = input("post.en_type");//工程类型
//        $id = 4;
            $data = MaterialTrackingDivision::all(['pid' => $id, 'type' => 3]);
            return json(["code"=>1,"data"=>$data]);
        }
    }

    /**
     * 获取所有工序下的所有控制点
     * @param $id
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getAllControlerPoint()
    {
        if(request()->isAjax()){

            $division_id = $this->request->param('division_id');

//            $division_id = 10;

            if ($this->request->has('ma_division_id')) {
                $par['ma_division_id'] = $this->request->param('ma_division_id');
            }

            $allControllerPoint = Db::name("quality_division_controlpoint_relation")->alias('a')
                ->join('controlpoint b', 'a.control_id=b.id', 'left')
                ->where(["a.type"=>1,"a.division_id"=>$division_id,"a.status"=>1])
                ->field('a.id,b.name,a.ma_division_id,a.control_id')
                ->select();


            if(!empty($allControllerPoint))
            {
                foreach ($allControllerPoint as $key=>$val)
                {
                    $search = array();
                    $search['a.type'] = 1;
                    $search['a.contr_relation_id'] = $val["id"];

                    $upload_form_info = Db::name("quality_upload")->alias('a')
                    ->join('attachment b', 'a.attachment_id=b.id', 'left')
                    ->join('admin c', 'b.user_id=c.id', 'left')
                    ->join('admin_group d', 'c.admin_group_id=d.id')
                    ->where($search)
                    ->field('a.id,a.data_name,a.attachment_id,a.type,c.nickname,d.name,b.create_time')
                    ->select();

                    if(!empty($upload_form_info))
                    {
                        foreach ($upload_form_info as $a=>$b)
                        {
                            //查询所属的工序名
                            $name = Db::name("materialtrackingdivision")->field("name as ma_name")->where("id",$val["ma_division_id"])->find();

                            $upload_form_info[$a]["ma_name"] = $name["ma_name"];

                        }
                    }
                    $allControllerPoint[$key]["upload_form_list"] = $upload_form_info;
                }
            }
            return json(["code"=>1,"data"=>$allControllerPoint]);
        }
    }

    /**
     * 添加电子文件挂接
     */
    public function electronicFileHang()
    {
        try{
            if(request()->isAjax()){
                //实例化模型类
                $Fileelectronic = new FileelectronicModel();
                $Document = new DocumentModel();
                $upload = new UploadModel();

                $param = input('post.');

                //需要的是一个id数组
                //用type区分图纸管理、文档管理、质量管理
                //type=paper，type=doc,type=quality
                $file_id_data = $param["id"];//前台传过来的id数组
                $type = $param["type"];//类型
                $fpd_id = $param["fpd_id"];//档案管理-待整理文件表的自增id

                switch ($type)
                {
                    case "paper":
                        break;
                    case "doc":
                        if(!empty($file_id_data))
                        {
                            foreach ($file_id_data as $key=>$val)
                            {
                                //判断当前控制点是否存在数据库中
                                $result = $Fileelectronic->getid($type,$fpd_id,$val);
                                if($result["id"])
                                {
                                    unset($file_id_data[$key]);
                                }
                            }

                            if(!empty($file_id_data))
                            {
                                foreach ($file_id_data as $k=>$v)
                                {
                                    //根据图纸管理、文档管理、质量管理中的文件id
                                    $document_info = $Document->getOne($v);
                                    if(!empty($document_info))
                                    {
                                        $data = [
                                            "fpd_id" => $fpd_id,//返回的fengning_file_pending_documents表的id
                                            "type" => $type,//paper图纸管理，doc文档管理，quality质量管理
                                            "file_id" => $v,//对应的是图纸管理，文档管理，质量管理表的文件id
                                            "electronic_file_name" => $document_info["docname"],//文件名称
                                            "type_code" => "文档",//类型代码
                                            "attachment_id" => $document_info["attachmentId"],//对应attachment表中的上传文件id
                                            "date" => date("Y-m-d",time())
                                        ];
                                        $Fileelectronic->insertFe($data);
                                    }
                                }
                            }

                            return ['code' => 1,'msg' => '添加成功'];
                        }
                        else
                        {
                            return ['code' => -1,'msg' => ''];
                        }
                        break;
                    case "quality":
                        if(!empty($file_id_data))
                        {
                            foreach ($file_id_data as $key=>$val)
                            {
                                //判断当前控制点是否存在数据库中
                                $result = $Fileelectronic->getid($type,$fpd_id,$val);
                                if($result["id"])
                                {
                                    unset($file_id_data[$key]);
                                }
                            }

                            if(!empty($file_id_data))
                            {
                                foreach ($file_id_data as $k=>$v)
                                {
                                    //根据图纸管理、文档管理、质量管理中的文件id
                                    $quality_info = $upload->getOne($v);
                                    if(!empty($quality_info))
                                    {
                                        $data = [
                                            "fpd_id" => $fpd_id,//返回的fengning_file_pending_documents表的id
                                            "type" => $type,//paper图纸管理，doc文档管理，quality质量管理
                                            "file_id" => $v,//对应的是图纸管理，文档管理，质量管理表的文件id
                                            "electronic_file_name" => $quality_info["data_name"],//文件名称
                                            "type_code" => "质量表单",//类型代码
                                            "attachment_id" => $quality_info["attachment_id"],//对应attachment表中的上传文件id
                                            "date" => date("Y-m-d",time())
                                        ];
                                        $Fileelectronic->insertFe($data);
                                    }
                                }
                            }

                            return ['code' => 1,'msg' => '添加成功'];
                        }
                        break;
                }

            }
        }catch (PDOException $e){
            return ['code' => -1,'msg' => $e->getMessage()];
        }
    }

    /**
     * 添加电子文件挂接的文件部位
     * @return array
     */
    public function addFilePart()
    {
        if(request()->isAjax()){
            //实例化模型类
            $model = new FileelectronicModel();
            $param = input('post.');
            $id = $param["id"];//选择要添加文件部位的电子文件挂接表的id
            $file_part = $param["file_part"];//前台传来的检验批名字数组

            //把传过来的数组用implode函数转成字符串
            if(!empty($file_part))
            {
                $new_file_part = implode(",",$file_part);
            }else
            {
                $new_file_part = "";
            }
            $data = ["id"=>$id,"file_part"=>$new_file_part];

            $flag = $model->editFe($data);

            return $flag;

        }
    }

    /**
     * 电子文件挂接删除
     * @return array
     */
    public function delElectronicFile()
    {
        if(request()->isAjax()){
            //实例化model类型
            $model = new FileelectronicModel();
            $param = input('post.');
            $flag = $model->delFe($param['id']);
            //删除成功返回提示信息
            return $flag;
        }
    }

    /**
     * 点击返回上边的档案管理-待整理文件时，全部删除该自动生成id下的全部电子挂接文件
     * @return array
     */
    public function delAllElectronicFile()
    {
        if(request()->isAjax()){
            //实例化model类型
            $model = new FileelectronicModel();
            $param = input('post.');
            $flag = $model->delAllFe($param['fpd_id']);
            //删除成功返回提示信息
            return $flag;
        }
    }

    /**********************************添加整理文件*********************/
    /**
     * 新增/编辑
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function addFilePendDoc()
    {
        if(request()->isAjax()){
            //实例化模型类
            $model = new Filependingdocuments();//档案管理-待整理文件

            $param = input('post.');

            if(empty($param['id']))//id为空时表示新增
            {
                $flag = $model->insertPd($param);
                return json($flag);
            }else{
                $flag = $model->editPd($param);
                return json($flag);
            }
        }
    }

    /**
     * 模板下载
     * @return \think\response\Json
     */
    public function excelDownload()
    {
        $filePath = "./static/awaitfile/awaitfile.xls";
        if(!file_exists($filePath)){
            return json(['code' => '-1','msg' => '文件不存在']);
        }else if(request()->isAjax()){
            return json(['code' => 1]); // 文件存在，告诉前台可以执行下载
        }else{
            $fileName = '导入模板-待整理文件.xls';
            $file = fopen($filePath, "r"); //   打开文件
            //输入文件标签
            $fileName = iconv("utf-8","gb2312",$fileName);
            Header("Content-type:application/octet-stream ");
            Header("Accept-Ranges:bytes ");
            Header("Accept-Length:   " . filesize($filePath));
            Header("Content-Disposition:   attachment;   filename= " . $fileName);
            //   输出文件内容
            echo fread($file, filesize($filePath));
            fclose($file);
            exit;
        }
    }

    /**
     * 待整理文件excel表格导入
     * @return array|\think\response\Json
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function importExcel()
    {
        $classifyid = input('post.classifyid');
        if(empty($classifyid)){
            return  json(['code' => -1,'data' => '','msg' => '请选择分组']);
        }
        $file = request()->file('file');

        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/file/branch/import');
        if($info){
            // 调用插件PHPExcel把excel文件导入数据库
            Loader::import('PHPExcel\Classes\PHPExcel', EXTEND_PATH);
            $exclePath = $info->getSaveName();  //获取文件名
            $file_name = ROOT_PATH . 'public' . DS . 'uploads/file/branch/import' . DS . $exclePath;   //上传文件的地址
            // 当文件后缀是xlsx 或者 csv 就会报：the filename xxx is not recognised as an OLE file错误
            $extension = get_extension($file_name);
            if ($extension =='xlsx') {
                $objReader = new \PHPExcel_Reader_Excel2007();
                $obj_PHPExcel = $objReader->load($file_name);
            } else if ($extension =='xls') {
                $objReader = new \PHPExcel_Reader_Excel5();
                $obj_PHPExcel = $objReader->load($file_name);
            } else if ($extension=='csv') {
                $PHPReader = new \PHPExcel_Reader_CSV();
                //默认输入字符集
                $PHPReader->setInputEncoding('GBK');
                //默认的分隔符
                $PHPReader->setDelimiter(',');
                //载入文件
                $obj_PHPExcel = $PHPReader->load($file_name);
            }else{
                return  json(['code' => -1,'data' => '','msg' => '请选择正确的模板文件']);
            }
            if(!is_object($obj_PHPExcel)){
                return  json(['code' => -1,'data' => '','msg' => '请选择正确的模板文件']);
            }
            $excel_array= $obj_PHPExcel->getsheet(0)->toArray();   // 转换第一页为数组格式

            // 验证格式 ---- 去除顶部菜单名称中的空格，并根据名称所在的位置确定对应列存储什么值
            $serial_number_index = -1;//序号
            $classification_number_index = -1;//分类号
            $classification_name_index = -1;//分类名称
            $document_title_index = -1;//文件题名
            $responsible_person_index = -1;//责任者
            $storage_time_index = -1;//保管期限
            $filing_date_index = -1;//归档日期
            $page_number_index = -1;//页数
            $document_number_index = -1;//文件编号
            $mutual_sight_number_index = -1;//互见号
            $archival_portion_index = -1;//归档份数
            $library_number_index = -1;//库位号
            $change_notice_number_index = -1;//变更通知单编号
            $remark_index = -1;//备注
            $written_time_index = -1;//成文日期
            $page_num_index = -1;//页号
            $bids_index = -1;//标段
            $founder_index = -1;//创建人
            $compiling_unit_index = -1;//编制单位

            foreach ($excel_array[0] as $k=>$v){

                $str = preg_replace('/[ ]/', '', $v);
                if ($str == '序号'){
                    $serial_number_index = $k;
                }else if ($str == '分类号'){
                    $classification_number_index = $k;
                }else if($str == '分类名称'){
                    $classification_name_index = $k;
                }else if($str == '文件题名'){
                    $document_title_index = $k;
                }else if($str == '责任者'){
                    $responsible_person_index = $k;
                }else if($str == '保管期限'){
                    $storage_time_index = $k;
                }else if($str == '归档日期'){
                    $filing_date_index = $k;
                }else if($str == '页数'){
                    $page_number_index = $k;
                }else if($str == '文件编号'){
                    $document_number_index = $k;
                }else if($str == '互见号'){
                    $mutual_sight_number_index = $k;
                }else if($str == '归档份数'){
                    $archival_portion_index = $k;
                }else if($str == '库位号'){
                    $library_number_index = $k;
                }else if($str == '变更通知单编号'){
                    $change_notice_number_index = $k;
                }else if($str == '备注'){
                    $remark_index = $k;
                }else if($str == '成文日期'){
                    $written_time_index = $k;
                }else if($str == '页号'){
                    $page_num_index = $k;
                }else if($str == '标段'){
                    $bids_index = $k;
                }else if($str == '创建人'){
                    $founder_index = $k;
                }else if($str == '编制单位'){
                    $compiling_unit_index = $k;
                }

            }
            if($serial_number_index == -1 || $classification_number_index == -1 || $classification_name_index == -1 ||
                $document_title_index == -1 || $responsible_person_index == -1 || $storage_time_index == -1 || $filing_date_index == -1 ||
                $page_number_index == -1 || $document_number_index == -1 || $mutual_sight_number_index == -1 || $archival_portion_index == -1 ||
                $library_number_index == -1 || $change_notice_number_index == -1 || $remark_index == -1 || $written_time_index == -1 || $page_num_index == -1 ||
                $bids_index == -1 || $founder_index == -1 || $compiling_unit_index){

                $json_data['code'] = -1;
                $json_data['msg'] = '文件内容格式不对';
                return json($json_data);
            }
            $insertData = [];
            foreach($excel_array as $k=>$v){
                if($k > 3){

                    $insertData[$k]['serial_number'] = $v[$serial_number_index];
                    $insertData[$k]['classification_number'] = $v[$classification_number_index];
                    $insertData[$k]['classification_name'] = $v[$classification_name_index];
                    $insertData[$k]['document_title'] = $v[$document_title_index];
                    $insertData[$k]['responsible_person'] = $v[$responsible_person_index];
                    $insertData[$k]['storage_time'] = $v[$storage_time_index];
                    $insertData[$k]['filing_date'] = $v[$filing_date_index];
                    $insertData[$k]['page_number'] = $v[$page_number_index];
                    $insertData[$k]['document_number'] = $v[$document_number_index];
                    $insertData[$k]['mutual_sight_number'] = $v[$mutual_sight_number_index];
                    $insertData[$k]['archival_portion'] = $v[$archival_portion_index];
                    $insertData[$k]['library_number'] = $v[$library_number_index];
                    $insertData[$k]['change_notice_number'] = $v[$change_notice_number_index];
                    $insertData[$k]['remark'] = $v[$remark_index];
                    $insertData[$k]['written_time'] = $v[$written_time_index];
                    $insertData[$k]['page_num'] = $v[$page_num_index];
                    $insertData[$k]['bids'] = $v[$bids_index];
                    $insertData[$k]['founder'] = $v[$founder_index];
                    $insertData[$k]['compiling_unit'] = $v[$compiling_unit_index];

                }
            }
            $success = Db::name('file_pending_documents')->insertAll($insertData);
            if($success !== false){
                return  json(['code' => 1,'data' => '','msg' => '导入成功']);
            }else{
                return json(['code' => -1,'data' => '','msg' => '导入失败']);
            }
        }
    }

    /**
     * 新增或编辑待整理文件
     */
    public function addoredit()
    {
        return $this->fetch();
    }
}