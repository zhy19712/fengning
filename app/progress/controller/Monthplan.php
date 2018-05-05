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
 * @package app\quality\controller
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

    public function assview()
    {
        return $this->fetch();
    }


    public function tree()
    {
        if ($this->request->isAjax()){
            //实例化模型
            $model = new MonthplanModel();
            //查询日志表
            $data = $model->getall();
            $res = tree($data);

            foreach ((array)$res as $a => $b) {
                $b['id'] = strval($b['id']);
                $res[$a] = json_encode($b);
            }
            return json($res);
        }
    }
    /**********************************月度计划************************/
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

        if($this->request->isAjax()){

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

    public function progress_monthplan($id, $draw, $table, $search, $start, $length, $limitFlag, $order, $columns, $columnString)
    {
        //查询
        //$_type = $this->request->has('type') ? $this->request->param('type') : "";
        //$_use = $this->request->has('use') ? $this->request->param('use') : "";
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
            $attachment = Db::name("attachment")->where("id",$data_info["attachment_id"])->find();
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
            Db::name('attachment')->where("id",$data_info["attachment_id"])->delete();

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




}

