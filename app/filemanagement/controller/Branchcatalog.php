<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/4/27
 * Time: 15:08
 */
/**
 * 档案管理-分支目录管理
 * Class Branchcatalog
 * @package app\filemanagement\controller
 */
namespace app\filemanagement\controller;
use app\admin\controller\Permissions;
use app\filemanagement\model\FilebranchtypeModel;//档案管理-分支目录管理-项目分类
use app\filemanagement\model\FilebranchModel;//档案管理-分支目录管理
use think\exception\PDOException;
use think\Loader;
use think\Db;
use think\Request;
use think\Session;

class Branchcatalog extends Permissions
{
    /**
     * 模板首页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }


    /*
     * 项目分类树
     * @return mixed|\think\response\Json
     */
    public function projecttree()
    {
        if($this->request->isAjax()){
            //实例化模型类
            $model = new FilebranchtypeModel();
            //获取项目分类表中的所有的数据
            $data = $model->getall();
            if(!empty($data))
            {
                $res = tree($data);
                foreach ((array)$res as $k => $v) {
                    $v['id'] = strval($v['id']);
                    $res[$k] = json_encode($v);
                }
            }else
            {
                $res = [];
            }
            return json($res);
        }
    }

    /**
     * 新增 或者 编辑 项目分类树
     * @return mixed|\think\response\Json
     */
    public function editNode()
    {
        if(request()->isAjax()){
            //实例化模型类
            $model = new FilebranchtypeModel();
            $param = input('post.');
            /**
             * 前台需要传递的是 pid 父级节点编号,id自增id,name节点名称
             */
            if(empty($param['id']))//id为空时表示新增项目分类节点
            {
                $data = [
                    'pid' => $param['pid'],
                    'name' => $param['name']
                ];
                $flag = $model->insertNode($data);
                return json($flag);
            }else{
                $data = [
                    'id' => $param['id'],
                    'name' => $param['name']
                ];
                $flag = $model->editNode($data);
                return json($flag);
            }
        }
    }

    /**
     * 删除项目分类树
     * @return \think\response\Json
     */
    public function delNode()
    {
        if (request()->isAjax()){
            //实例化模型类
            $model = new FilebranchtypeModel();
            $branch_model = new FilebranchModel();
            $id = input('post.id');
            //因为固定住前四项删不掉，所以id为1,2,3,4,5的删不掉
            $judge_id = array('1','2','3','4','5');
            if(in_array($id,$judge_id))
            {
                return json(['code' => -1, 'msg' => '系统节点不允许操作！']);
            }
            //判断当前节点下是否有数据，有数据的话是不能删除的
            $result = $branch_model->judgeClassifyid($id);
            if(!empty($result))
            {
                return json(['code' => -1, 'msg' => '包含数据，不允许删除！']);
            }
            //节点树对应下的项目分类


            //最后删除此节点
            $flag = $model->delNode($id);
            return json($flag);
        }
    }

    /*****************************右侧项目分类*************************/
    /**
     * datables表格
     */
    public function table()
    {
        if (request()->isAjax()){
        //实例化模型类
        $model = new FilebranchModel();
        $classifyid = input('post.id');
        if($classifyid == 1)
        {
            $search = [];
        }else
        {
            $search = [
                "classifyid" => $classifyid
            ];

        }

        $data = $model->getAll($search);
        $res = tree($data);

        foreach ((array)$res as $k => $v) {
            $v['id'] = strval($v['id']);
            $res[$k] = $v;
        }
        return json($res);
        }
    }

    /*
     * 获取一条分类项目信息
     */
    public function getindex()
    {
        if(request()->isAjax()){
            //实例化模型类
            $model = new FilebranchModel();
            $param = input('post.');
            $data = $model->getOne($param['id']);
            return json(['code'=> 1, 'data' => $data]);
        }
    }

    /**
     * 新增/编辑
     * @return mixed|\think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function editCate()
    {
        if(request()->isAjax()){
            //实例化模型类
            $model = new FilebranchModel();
            $param = input('post.');

            //前台传过来的id
            if(empty($param['id']))//id为空时表示新增
            {
                $data = [
                    "classifyid" => $param["classifyid"],//左侧分类树id
                    "parent_code" => $param["parent_code"],//父节点（序号）
                    "code" => $param["code"],//序号
                    "class_name" => $param["class_name"],//名称
                    "pid" => $param["pid"]//父级id
                ];
                $flag = $model->insertCate($data);
                return json($flag);
            }else{
                $data = [
                    "id" => $param["id"],
                    "classifyid" => $param["classifyid"],
                    "parent_code" => $param["parent_code"],
                    "code" => $param["code"],
                    "class_name" => $param["class_name"]
                ];
                $flag = $model->editCate($data);
                return json($flag);
            }
        }
    }

    /**
     * 删除
     * @return array
     */
    public function delCate()
    {
        if(request()->isAjax()){
            //实例化model类型
            $model = new FilebranchModel();
            $param = input('post.');
            //首先判断一下删除的是否存在下级
            $info = $model ->judgeId($param['id']);
            if(empty($info))//没有下级直接删除
            {
                $flag = $model->delCate($param['id']);
                return $flag;
            }else
            {
                return ['code' => -1, 'msg' => '请先删除下级！'];
            }
        }
    }

    /**
     * 模板下载
     * @return \think\response\Json
     */
    public function excelDownload()
    {
        $filePath = "./static/branch/branch_directory.xls";
        if(!file_exists($filePath)){
            return json(['code' => '-1','msg' => '文件不存在']);
        }else if(request()->isAjax()){
            return json(['code' => 1]); // 文件存在，告诉前台可以执行下载
        }else{
            $fileName = '导入模板-分支目录.xls';
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
     * 分支目录excel表格导入
     * @return array|\think\response\Json
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function importExcel()
    {
        $selfid = input('param.selfid');
        if(empty($selfid)){
            return  json(['code' => 1,'data' => '','msg' => '请选择分组']);
        }
        $file = request()->file('file');
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/safety/import/specialequipmentmanage');
        if($info){
            // 调用插件PHPExcel把excel文件导入数据库
            Loader::import('PHPExcel\Classes\PHPExcel', EXTEND_PATH);
            $exclePath = $info->getSaveName();  //获取文件名
            $file_name = ROOT_PATH . 'public' . DS . 'uploads/safety/import/specialequipmentmanage' . DS . $exclePath;   //上传文件的地址
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
            }
            $excel_array= $obj_PHPExcel->getsheet(0)->toArray();   // 转换第一页为数组格式
            // 验证格式 ---- 去除顶部菜单名称中的空格，并根据名称所在的位置确定对应列存储什么值
            $equip_name_index = $model_index = $equip_num_index = $manufactur_unit_index = $date_production_index = $current_state_index = $safety_inspection_num_index = $inspection_unit_index = $entry_time_index = $equip_state_index = $remark_index = -1;
            foreach ($excel_array[0] as $k=>$v){
                $str = preg_replace('/[ ]/', '', $v);
                if ($str == '设备名称'){
                    $equip_name_index = $k;
                }else if ($str == '型号'){
                    $model_index = $k;
                }else if($str == '设备编号'){
                    $equip_num_index = $k;
                }else if($str == '制造单位'){
                    $manufactur_unit_index = $k;
                }else if($str == '出厂日期'){
                    $date_production_index = $k;
                }else if($str == '当前状态'){
                    $current_state_index = $k;
                }else if($str == '安全检验合格证书编号'){
                    $safety_inspection_num_index = $k;
                }else if($str == '检验单位'){
                    $inspection_unit_index = $k;
                }else if($str == '进场时间'){
                    $entry_time_index = $k;
                }else if($str == '设备状态'){
                    $equip_state_index = $k;
                }else if($str == '备注'){
                    $remark_index = $k;
                }
            }
//            return json(['1'=> $number_index, '2' => $equip_name_index,'3' =>$model_index,'4' => $equip_num_index,'5' => $manufactur_unit_index,'6' => $date_production_index,'7' => $current_state_index,'8' => $safety_inspection_num_index,'9' => $inspection_unit_index,'10' => $entry_time_index,'11' => $equip_state_index,'12' => $remark_index]);

            if($equip_name_index == -1 || $model_index == -1 || $equip_num_index == -1 || $manufactur_unit_index == -1 || $date_production_index == -1 || $current_state_index == -1 || $safety_inspection_num_index == -1 || $inspection_unit_index == -1 || $entry_time_index == -1 || $equip_state_index == -1 || $remark_index == -1){
                $json_data['code'] = 0;
                $json_data['info'] = '文件内容格式不对';
                return json($json_data);
            }
            $insertData = [];
            foreach($excel_array as $k=>$v){
                if($k > 0){

                    $insertData[$k]['equip_name'] = $v[$equip_name_index];
                    $insertData[$k]['model'] = $v[$model_index];
                    $insertData[$k]['equip_num'] = $v[$equip_num_index];
                    $insertData[$k]['manufactur_unit'] = $v[$manufactur_unit_index];
                    $insertData[$k]['date_production'] = $v[$date_production_index];
                    $insertData[$k]['current_state'] = $v[$current_state_index];
                    $insertData[$k]['safety_inspection_num'] = $v[$safety_inspection_num_index];
                    $insertData[$k]['inspection_unit'] = $v[$inspection_unit_index];
                    $insertData[$k]['entry_time'] = $v[$entry_time_index];
                    $insertData[$k]['equip_state'] = $v[$equip_state_index];
                    $insertData[$k]['remark'] = $v[$remark_index];
                    $insertData[$k]['input_time'] = date('Y-m-d H:i:s');
                    $insertData[$k]['owner'] = session('username');
                    $insertData[$k]['selfid'] = $selfid;

                }
            }
            $success = Db::name('safety_special_equipment_management')->insertAll($insertData);
            if($success !== false){
                return  json(['code' => 1,'data' => '','msg' => '导入成功']);
            }else{
                return json(['code' => -1,'data' => '','msg' => '导入失败']);
            }
        }
    }
}