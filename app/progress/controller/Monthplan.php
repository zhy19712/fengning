<?php
/**
 * Created by PhpStorm.
 * User: 19113
 * Date: 2018/4/19
 * Time: 11:03
 */
/**
 * 进度管理-月度计划
 * Class Progressversion
 */
namespace app\progress\controller;

use app\admin\controller\Permissions;
use think\Session;
use think\Request;
use think\exception\PDOException;
use app\admin\model\AdminGroup;//组织机构
use app\admin\model\Admin;//用户表
use think\Loader;
use think\Db;
use think\Controller;
use app\progress\model\MonthplanModel;

class Monthplan extends Permissions
{
    /**
     * 进度版本管理模板首页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }


    public function tree()
    {
        if ($this->request->isAjax()){
            //实例化模型
            $model = new MonthplanModel();
            //查询监理日志表
            $data = $model->getall();
            $res = tree($data);

            foreach ((array)$res as $a => $b) {
                $b['id'] = strval($b['id']);
                $res[$a] = json_encode($b);
            }
            return json($res);
        }
    }
    /**********************************监理日志************************/
    /**
     * 获取一条信息
     */
    public function getindex()
    {
        if(request()->isAjax()){
            //实例化模型类
            $model = new MonthPlanModel();
            $param = input('post.');
            $data = $model->getOne($param['id']);
            return json(['code'=> 1, 'data' => $data]);
        }
        return $this->fetch();
    }



    public function getalldata()
    {
        if(request()->isAjax()){

            return $this->datatablesPre();


        }
    }

    function datatablesPre()
    {
        //接收表名，列名数组 必要
        $columns = $this->request->param('columns/a');
        //获取查询条件
        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;
        $table = $this->request->param('tableName');
        //接收查询条件，可以为空
        $columnNum = sizeof($columns);
        $columnString = '';
        for ($i = 0; $i < $columnNum; $i++) {
            if ($columns[$i]['searchable'] == 'true') {
                $columnString = $columns[$i]['name'] . '|' . $columnString;
            }
        }
        $columnString = substr($columnString, 0, strlen($columnString) - 1);
        //获取Datatables发送的参数 必要
        $draw = $this->request->has('draw') ? $this->request->param('draw', 0, 'intval') : 0;
        //排序列
        $order_column = $this->request->param('order/a')['0']['column'];
        //ase desc 升序或者降序
        $order_dir = $this->request->param('order/a')['0']['dir'];

        $order = "";
        if (isset($order_column)) {
            $i = intval($order_column);
            $order = $columns[$i]['name'] . ' ' . $order_dir;
        }
        //搜索
        //获取前台传过来的过滤条件
        $search = $this->request->param('search/a')['value'];
        //分页
        $start = $this->request->has('start') ? $this->request->param('start', 0, 'intval') : 0;
        $length = $this->request->has('length') ? $this->request->param('length', 0, 'intval') : 0;
        $limitFlag = isset($start) && $length != -1;
        //新建的方法名与数据库表名保持一致
        return $this->$table($id, $draw, $table, $search, $start, $length, $limitFlag, $order, $columns, $columnString);
    }

    public function norm_file($id, $draw, $table, $search, $start, $length, $limitFlag, $order, $columns, $columnString)
    {
        //查询
        //条件过滤后记录数 必要
        $recordsFiltered = 0;
        $recordsFilteredResult = array();
        $node = new NormModel();
        $idArr = $node->cateTree($id);
        $idArr[] = $id;
        //表的总记录数 必要
        $recordsTotal = Db::name($table)->whereIn('nodeId', $idArr)->count(0);
        if (strlen($search) > 0) {
            //有搜索条件的情况
            if ($limitFlag) {
                //*****多表查询join改这里******
                $recordsFilteredResult = Db::name($table)
                    ->field('standard_number,standard_name,material_date,alternate_standard,remark,id')
                    ->whereIn('nodeId', $idArr)
                    ->where($columnString, 'like', '%' . $search . '%')
                    ->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = sizeof($recordsFilteredResult);
            }
        } else {
            //没有搜索条件的情况
            if ($limitFlag) {
                //*****多表查询join改这里******
                $recordsFilteredResult = Db::name($table)
                    ->field('standard_number,standard_name,material_date,alternate_standard,remark,id')
                    ->whereIn('nodeId', $idArr)
                    ->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = $recordsTotal;
            }
        }
        $temp = array();
        $infos = array();
        foreach ($recordsFilteredResult as $key => $value) {
            $length = sizeof($columns);
            for ($i = 0; $i < $length; $i++) {
                array_push($temp, $value[$columns[$i]['name']]);
            }
            $infos[] = $temp;
            $temp = [];
        }
        return json(['draw' => intval($draw), 'recordsTotal' => intval($recordsTotal), 'recordsFiltered' => $recordsFiltered, 'data' => $infos]);
    }

    public function quality_template($id, $draw, $table, $search, $start, $length, $limitFlag, $order, $columns, $columnString)
    {
        //查询
        $_type = $this->request->has('type') ? $this->request->param('type') : "";
        $_use = $this->request->has('use') ? $this->request->param('use') : "";
        //条件过滤后记录数 必要
        $recordsFiltered = 0;
        $recordsFilteredResult = array();
        //表的总记录数 必要
        $recordsTotal = Db::name($table)->count(0);
        if (strlen($search) > 0) {

            if ((!empty($_type)) || (!empty($_use))) {
                if ($limitFlag) {
                    if ((!empty($_type)) && (!empty($_use))) {
                        $wherestr['type'] = $_type;
                        $wherestr['use'] = $_use;
                    } else if (!empty($_type)) {
                        $wherestr['type'] = $_type;

                    } else {
                        $wherestr['use'] = $_use;
                    }
                    $recordsTotal = Db::name($table)->where($wherestr)->count(0);
                    $recordsFilteredResult = Db::name($table)
                        ->where($wherestr)
                        ->where($columnString, 'like', '%' . $search . '%')
                        ->order($order)->limit(intval($start), intval($length))->select();
                    $recordsFiltered = sizeof($recordsFilteredResult);
                }
            } else {
                $recordsFilteredResult = Db::name($table)
                    ->where($columnString, 'like', '%' . $search . '%')
                    ->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = sizeof($recordsFilteredResult);
            }
        } else {
            //没有搜索条件的情况
            if ($limitFlag) {
                if ((!empty($_type)) || (!empty($_use))) {
                    if ($limitFlag) {
                        if ((!empty($_type)) && (!empty($_use))) {
                            $wherestr['type'] = $_type;
                            $wherestr['use'] = $_use;
                        } else if (!empty($_type)) {
                            $wherestr['type'] = $_type;

                        } else {
                            $wherestr['use'] = $_use;
                        }
                        $recordsTotal = Db::name($table)->where($wherestr)->count(0);
                        $recordsFilteredResult = Db::name($table)
                            ->where($wherestr)
                            ->order($order)->limit(intval($start), intval($length))->select();
                        $recordsFiltered = $recordsTotal;
                    }
                } else {
                    $recordsFilteredResult = Db::name($table)
                        ->order($order)->limit(intval($start), intval($length))->select();
                    $recordsFiltered = $recordsTotal;
                }
            }
        }
        $temp = array();
        $infos = array();
        foreach ($recordsFilteredResult as $key => $value) {
            $length = sizeof($columns);
            for ($i = 0; $i < $length; $i++) {
                array_push($temp, $value[$columns[$i]['name']]);
            }
            $infos[] = $temp;
            $temp = [];
        }
        return json(['draw' => intval($draw), 'recordsTotal' => intval($recordsTotal), 'recordsFiltered' => $recordsFiltered, 'data' => $infos]);
    }

    public function upload($module = 'admin', $use = 'admin_thumb')
    {
        if ($this->request->file('file')) {
            $file = $this->request->file('file');
        } else {
            $res['code'] = 1;
            $res['msg'] = '没有上传文件';
            return json($res);
        }
        $module = $this->request->has('module') ? $this->request->param('module') : $module;//模块
        $web_config = Db::name('webconfig')->where('web', 'web')->find();
        $info = $file->validate(['size' => $web_config['file_size'] * 1024, 'ext' => $web_config['file_type']])->rule('date')->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . $module . DS . $use);
        if ($info) {
            //写入到附件表
            $data = [];
            $data['module'] = $module;
            $data['filename'] = $info->getFilename();//文件名
            $data['filepath'] = DS . 'uploads' . DS . $module . DS . $use . DS . $info->getSaveName();//文件路径
            $data['fileext'] = $info->getExtension();//文件后缀
            $data['filesize'] = $info->getSize();//文件大小
            $data['create_time'] = time();//时间
            $data['uploadip'] = $this->request->ip();//IP
            $data['user_id'] = Session::has('admin') ? Session::get('admin') : 0;
            if ($data['module'] == 'admin') {
                //通过后台上传的文件直接审核通过
                $data['status'] = 1;
                $data['admin_id'] = $data['user_id'];
                $data['audit_time'] = time();
            }
            $data['use'] = $this->request->has('use') ? $this->request->param('use') : $use;//用处
            $res['id'] = Db::name('attachment')->insertGetId($data);
            $res['src'] = DS . 'uploads' . DS . $module . DS . $use . DS . $info->getSaveName();
            $res['code'] = 2;
            addlog($res['id']);//记录日志
            return json($res);
        } else {
            // 上传失败获取错误信息
            return $this->error('上传失败：' . $file->getError());
        }
    }

    public function controlpoint($id, $draw, $table, $search, $start, $length, $limitFlag, $order, $columns, $columnString)
    {
        //查询
        $c = new ControlPoint();
        $idArr = $c->getChilds($id);
        $idArr[] = $id;
        //条件过滤后记录数 必要
        $recordsFiltered = 0;
        $recordsFilteredResult = array();
        //表的总记录数 必要
        $recordsTotal = Db::name($table)->whereIn('ProcedureId', $idArr)->count(0);
        if (strlen($search) > 0) {
            //有搜索条件的情况
            if ($limitFlag) {
                $recordsFilteredResult = Db::name($table)
                    ->whereIn('ProcedureId', $idArr)
                    ->where($columnString, 'like', '%' . $search . '%')
                    ->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = sizeof($recordsFilteredResult);
            }
        } else {
            //没有搜索条件的情况
            if ($limitFlag) {
                //*****多表查询join改这里******
                $recordsFilteredResult = Db::name($table)
                    ->whereIn('ProcedureId', $idArr)
                    ->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = $recordsTotal;
            }
        }
        $temp = array();
        $infos = array();
        foreach ($recordsFilteredResult as $key => $value) {
            $length = sizeof($columns);
            for ($i = 0; $i < $length; $i++) {
                array_push($temp, $value[$columns[$i]['name']]);
            }
            $infos[] = $temp;
            $temp = [];
        }
        return json(['draw' => intval($draw), 'recordsTotal' => intval($recordsTotal), 'recordsFiltered' => $recordsFiltered, 'data' => $infos]);
    }



    /**
     * 上传
     * @return \think\response\Json
     */
    public function add()
    {
        if(request()->isAjax()){
            //实例化模型类
            $model = new MonthplanModel();
            $admin = new Admin();
            $group = new AdminGroup();

            $param = input('post.');

            //获取当前时间的年月日
            $year = date("Y");
            $month = date("m");
            $day = date("d");

            //根据当前的上传时间获取父级节点id
            //1.查询当前的年份是否存在,如果年份不存在时，新建一条年份记录
            $search_info =[
                "year" => $year,
                "month" => "",
                "day" => ""
            ];
            $result = $model->getid($search_info);
            if(!$result['id'])//如果当前的年份不存在就新建当前年的记录
            {
                $data = [
                    "year" => $year,
                    "name" => $year."年",
                    'pid' => 1
                ];
                //新增一条年份
                $model -> insertLog($data);

                //新建一条月份记录
                $search_info1 =[
                    "year" => $year,
                    "month" => "",
                    "day" => ""
                ];
                $result1 = $model->getid($search_info1);
                $data1 = [
                    "year" => $year,
                    "month" => $month,
                    "name" => $month."月",
                    'pid' => $result1['id']
                ];
                //新增一条月份记录
                $model -> insertLog($data1);

                //新建一条日份记录
                $search_info2 =[
                    "year" => $year,
                    "month" => $month,
                    "day" => ""
                ];
                $result2 = $model->getid($search_info2);
                $admin_id = Session::get('current_id');
                $admininfo = $admin->getadmininfo($admin_id);
                $group = $group->getOne($admininfo["admin_group_id"]);
                $data2 = [
                    "year" => $year,
                    "month" => $month,
                    "day" => $day,
                    "name" => $day."日",
                    "pid" => $result2['id'],
                    "attachment_id" => $param["attachment_id"],//对应attachment文件上传表中的id
                    "filename" => date("YmdHis"),//默认上传的文件名为日期
                    "date" => date("Y-m-d H:i:s"),
                    "owner" => Session::get('current_name'),
                    "company" => $group["name"],//单位
                    "admin_group_id" => $admininfo["admin_group_id"]
                ];
                $flag = $model->insertLog($data2);
                return json($flag);


            }else{
                //2.查询当前的月份是否存在,如果月份不存在时，新建一条月份记录
                $search_info =[
                    "year" => $year,
                    "month" => $month,
                    "day" => ""
                ];
                $result = $model->getid($search_info);
                if(!$result['id'])//如果当前的月份不存在就新建当前月的记录
                {
                    $search_info1 =[
                        "year" => $year,
                        "month" => "",
                        "day" => ""
                    ];
                    $result1 = $model->getid($search_info1);

                    $data1 = [
                        "year" => $year,
                        "month" => $month,
                        "name" => $month."月",
                        'pid' => $result1['id']
                    ];
                    //新增一条月份
                    $model -> insertLog($data1);
                    //新建一条日份记录
                    $search_info2 =[
                        "year" => $year,
                        "month" => $month,
                        "day" => ""
                    ];
                    $result2 = $model->getid($search_info2);
                    $admin_id = Session::get('current_id');
                    $admininfo = $admin->getadmininfo($admin_id);
                    $group = $group->getOne($admininfo["admin_group_id"]);
                    $data2 = [
                        "year" => $year,
                        "month" => $month,
                        "day" => $day,
                        "name" => $day."日",
                        "pid" => $result2['id'],
                        "attachment_id" => $param["attachment_id"],//对应attachment文件上传表中的id
                        "filename" => date("YmdHis"),//默认上传的文件名为日期
                        "date" => date("Y-m-d H:i:s"),
                        "owner" => Session::get('current_name'),
                        "company" => $group["name"],//单位
                        "admin_group_id" => $admininfo["admin_group_id"]
                    ];
                    $flag = $model->insertLog($data2);
                    return json($flag);

                }else{
                    //3.如果当前的年份、月份都存在时，新增完整的一条信息
                    //查询当前登录的用户所属的组织机构名
                    $search_info =[
                        "year" => $year,
                        "month" => $month,
                        "day" => ""
                    ];
                    $result = $model->getid($search_info);
                    $admin_id = Session::get('current_id');
                    $admininfo = $admin->getadmininfo($admin_id);
                    $group = $group->getOne($admininfo["admin_group_id"]);
                    $data = [
                        "year" => $year,
                        "month" => $month,
                        "day" => $day,
                        "name" => $day."日",
                        "pid" => $result['id'],
                        "attachment_id" => $param["attachment_id"],//对应attachment文件上传表中的id
                        "filename" => date("YmdHis"),//默认上传的文件名为日期
                        "date" => date("Y-m-d H:i:s"),
                        "owner" => Session::get('current_name'),
                        "company" => $group["name"],//单位
                        "admin_group_id" => $admininfo["admin_group_id"]
                    ];
                    $flag = $model->insertLog($data);
                    return json($flag);
                }
            }
        }
    }

    /**
     * 编辑一条信息
     */
    public function edit()
    {
        if(request()->isAjax()){
            //实例化模型类
            $model = new MonthplanModel();
            $param = input('post.');
            $data = [
                'id' => $param['id'],//监日志自增id
                'filename' => $param['filename']//上传文件名
            ];
            $flag = $model->editLog($data);
            return json($flag);
        }
    }

    /**
     * 删除一条信息
     */
    public function del()
    {
        if (request()->isAjax()){
            //实例化model类型
            $model = new MonthplanModel();
            $id = input('post.id');//要删除的日志id
            //首先判断一下删除的只一天所属的月份下是否有其他日子
            $data_info = $model->getOne($id);

            $day_count = $model->getcount($data_info['pid']);

            $data_month = $model->getOne($data_info["pid"]);

            //如果一个月份下只有一条的话就删除这个月份
            if($day_count < 2)
            {
                $model -> delLog($data_info['pid']);

            }

            //判断年份下只有一条的话就删除这个年份
            $year_count = $model->getcount($data_month['pid']);
            if($year_count < 1)
            {
                //如果一个年份下只有一条的话就删除这个年份
                $model -> delLog($data_month['pid']);
            }

            //最后删除这条日志信息
            //查询attachment表中的文件上传路径
            $attachment = Db::name("attachment")
                          ->where("id",$data_info["attachment_id"])
                          ->find();
            $path = "." .$attachment['filepath'];
            $pdf_path = './uploads/temp/' . basename($path) . '.pdf';
            if($attachment['filepath'])
            {
                if(file_exists($path)){
                    unlink($path); //删除上传的图片或文件
                }
                if(file_exists($pdf_path)){
                    unlink($pdf_path); //删除生成的预览pdf
                }
            }

            //删除attachment表中对应的记录
            Db::name('attachment')
                ->where("id",$data_info["attachment_id"])
                ->delete();

            //最后删除这一条记录信息
            $flag = $model->delLog($id);
            return $flag;
        }
    }
    /***************************************三维模型******************/
    /**
     * 编辑一条图片位置信息
     */
    public function editPosition()
    {
        if(request()->isAjax()){
            //实例化模型类
            $model = new MonthplanModel();
            $param = input('post.');
            halt($param);
            $data = [
                'id' => $param['id'],//现场图片自增id
                'position' => $param['position']//位置信息
            ];
            $flag = $model->editLog($data);
            return json($flag);
        }
    }

    public function modelPicturePreview()
    {
        // 前台 传递 选中的 单元工程段号 编号 id
        if($this->request->isAjax()){
            $param = input('param.');
            $id = isset($param['id']) ? $param['id'] : -1;
            if($id == -1){
                return json(['code' => 0,'msg' => '编号有误']);
            }
            // 获取关联的模型图
            $picture = new PictureRelationModel();
            $data = $picture->getAllNumber([$id]);
            $picture_number = $data['picture_number_arr'];
            return json(['code'=>1,'number'=>$picture_number,'msg'=>'单元工程段号-模型图编号']);
        }
    }

    /**
     *
     *
     *
     *
     * //模型功能
     * 打开关联模型 页面 openModelPicture
     * @return mixed|\think\response\Json
     * @author hutao
     */
    public function openModelPicture()
    {
        // 前台 传递 选中的 单元工程段号的 id编号
        if($this->request->isAjax()){
            $param = input('param.');
            $id = isset($param['id']) ? $param['id'] : -1;
            if($id == -1){
                return json(['code' => 0,'msg' => '编号有误']);
            }
            // 获取工程划分下的 所有的模型图主键,编号,名称
            $picture = new PictureModel();
            $data = $picture->getAllName($id);
            return json(['code'=>1,'one_picture_id'=>$data['one_picture_id'],'data'=>$data['str'],'msg'=>'模型图列表']);
        }
        return $this->fetch('relationview');
    }

    /**
     * 关联模型图
     * @return \think\response\Json
     * @author hutao
     */
    public function addModelPicture()
    {
        // 前台 传递 单元工程段号(单元划分) 编号id  和  模型图主键编号 picture_id
        if($this->request->isAjax()){
            $param = input('param.');
            $relevance_id = isset($param['id']) ? $param['id'] : -1;
            $picture_id = isset($param['picture_id']) ? $param['picture_id'] : -1;
            if($relevance_id == -1 || $picture_id == -1){
                return json(['code' => 0,'msg' => '参数有误']);
            }
            // 是否已经关联过 picture_type  1工程划分模型 2 建筑模型 3三D模型
            $is_related = Db::name('quality_model_picture_relation')->where(['type'=>1,'relevance_id'=>$relevance_id])->value('id');
            $data['type'] = 1;
            $data['relevance_id'] = $relevance_id;
            $data['picture_id'] = $picture_id;
            $picture = new PictureRelationModel();
            if(empty($is_related)){
                // 关联模型图 一对一关联
                $flag = $picture->insertTb($data);
                return json($flag);
            }else{
                $data['id'] = $is_related;
                $flag = $picture->editTb($data);
                return json($flag);
            }
        }
    }

    // 此方法只是临时 导入模型图 编号和名称的 txt文件时使用
    // 不存在于 功能列表里面 后期可以删除掉
    // 获取txt文件内容并插入到数据库中 insertTxtContent
    public function insertTxtContent()
    {
        $filePath = './static/monthplan/GolIdTable.txt';
        if(!file_exists($filePath)){
            return json(['code' => '-1','msg' => '文件不存在']);
        }
        $files = fopen($filePath, "r") or die("Unable to open file!");
        $contents = $new_contents =[];
        while(!feof($files)) {
            $txt = iconv('gb2312','utf-8//IGNORE',fgets($files));
            $txt = str_replace('[','',$txt);
            $txt = str_replace(']','',$txt);
            $txt = str_replace("\r\n",'',$txt);
            $contents[] = $txt;
        }

        foreach ($contents as $v){
            $new_contents[] = explode(' ',$v);
        }

        $data = [];
        foreach ($new_contents as $k=>$val){
            $data[$k]['picture_name'] = trim(next($val));
            $data[$k]['picture_number'] = trim(next($val));
        }

        array_pop($data);

        $picture = new PictureModel();
        $picture->saveAll($data); // 使用saveAll 是因为 要 自动插入 时间
        fclose($files);
    }

  //模型功能
    /**
     * 搜索模型
     * @return \think\response\Json
     * @author hutao
     */
    public function searchModel()
    {
        // 前台 传递  选中的 单元工程段号的 id编号  和  搜索框里的值 search_name
        if($this->request->isAjax()){
            $param = input('param.');
            $id = isset($param['id']) ? $param['id'] : -1;
            $search_name = isset($param['search_name']) ? $param['search_name'] : -1;
            if($id == -1){
                return json(['code' => 0,'msg' => '请传递选中的单元工程段号的编号']);
            }if($id == -1 || $search_name == -1){
                return json(['code' => 0,'msg' => '请写入需要搜索的值']);
            }
            // 获取搜索的模型图主键,编号,名称
            $picture = new PictureModel();
            $data = $picture->getAllName($id,$search_name);
            return json(['code'=>1,'one_picture_id'=>$data['one_picture_id'],'data'=>$data['str'],'msg'=>'模型图列表']);
        }
    }




}

