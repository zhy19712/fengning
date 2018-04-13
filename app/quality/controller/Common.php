<?php
/**
 * Created by PhpStorm.
 * User: sir
 * Date: 2018/4/3
 * Time: 9:43
 */

namespace app\quality\controller;


use app\quality\model\UnitqualitymanageModel;
use app\quality\model\ScenePictureModel;
use think\Controller;
use think\Db;
use think\Session;

class Common extends Controller
{

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
            $columnString = $columns[$i]['name'] . '|' . $columnString;
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

    public function archive_atlas_cate($id, $draw, $table, $search, $start, $length, $limitFlag, $order, $columns, $columnString)
    {
        //查询
        //条件过滤后记录数 必要
        $recordsFiltered = 0;
        $recordsFilteredResult = array();
        //表的总记录数 必要
        $recordsTotal = Db::name($table)->count();
        if (strlen($search) > 0) {
            //有搜索条件的情况
            if ($limitFlag) {
                //*****多表查询join改这里******
                $recordsFilteredResult = Db::name($table)
                    ->field('picture_number,picture_name,picture_papaer_num,a1_picture,design_name,check_name,examination_name,completion_date,section,paper_category,id')
                    ->where($columnString, 'like', '%' . $search . '%')
                    ->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = sizeof($recordsFilteredResult);
            }
        } else {
            //没有搜索条件的情况
            if ($limitFlag) {
                //*****多表查询join改这里******
                $recordsFilteredResult = Db::name($table)
                    ->field('picture_number,picture_name,picture_papaer_num,a1_picture,design_name,check_name,examination_name,completion_date,section,paper_category,id')
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

    public function quality_unit($id, $draw, $table, $search, $start, $length, $limitFlag, $order, $columns, $columnString)
    {
        //查询
        //条件过滤后记录数 必要
        $recordsFiltered = 0;
        $recordsFilteredResult = array();
        //表的总记录数 必要
        $recordsTotal = Db::name($table)->where('division_id', $id)->count();
        if (strlen($search) > 0) {
            //有搜索条件的情况
            if ($limitFlag) {
                //*****多表查询join改这里******
                $recordsFilteredResult = Db::name($table)
                    ->field('serial_number,site,coding,hinge,pile_number,start_date,completion_date,id')
                    ->where('division_id', $id)
                    ->where($columnString, 'like', '%' . $search . '%')
                    ->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = sizeof($recordsFilteredResult);
            }
        } else {
            //没有搜索条件的情况
            if ($limitFlag) {
                //*****多表查询join改这里******
                $recordsFilteredResult = Db::name($table)
                    ->field('serial_number,site,coding,hinge,pile_number,start_date,completion_date,id')
                    ->where('division_id', $id)
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
     * 质量管理文件上传
     * @param string $module
     * @param string $use
     * @return \think\response\Json|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function upload($module = 'quality', $use = 'quality_thumb')
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
            if ($data['module'] = 'atlas') {
                //通过后台上传的文件直接审核通过
                $data['status'] = 1;
                $data['admin_id'] = $data['user_id'];
                $data['audit_time'] = time();
            }
            $data['use'] = $this->request->has('use') ? $this->request->param('use') : $use;//用处
            $res['id'] = Db::name('attachment')->insertGetId($data);
//            $res['filename'] = $info->getFilename();//文件名
            $res['src'] = DS . 'uploads' . DS . $module . DS . $use . DS . $info->getSaveName();
            $res['code'] = 2;
            addlog($res['id']);//记录日志
            return json($res);
        } else {
            // 上传失败获取错误信息
            return $this->error('上传失败：' . $file->getError());
        }
    }

    /**
     * 质量管理文件下载
     * @return \think\response\Json
     */
    public function download()
    {
        if (request()->isAjax()) {
            $id = input('param.id');//id
            $type_model = input('param.type_model');//model类名
            //拼接model类的地址
            $type_model = "app\\quality\\model\\".$type_model;
            $model = new $type_model;
            $param = $model->getOne($id);
            //查询attachment文件上传表中的文件上传路径
            $attachment = Db::name("attachment")->where("id", $param["attachment_id"])->find();
            //上传文件路径
            $path = $attachment["filepath"];
            if (!$path || !file_exists("." . $path)) {
                return json(['code' => '-1', 'msg' => '文件不存在']);
            }
            return json(['code' => 1]);
        }
        $id = input('param.id');

        $type_model = input('param.type_model');//model类名
        //拼接model类的地址
        $type_model = "app\\quality\\model\\".$type_model;
        $model = new $type_model();
        $param = $model->getOne($id);
        //查询attachment文件上传表中的文件上传路径
        $attachment = Db::name("attachment")->where("id", $param["attachment_id"])->find();
        //上传文件路径
        $path = $attachment["filepath"];

        $filePath = '.' . $path;
        $fileName = $param['filename'];
        $file = fopen($filePath, "r"); //   打开文件
        //输入文件标签
        $fileName = iconv("utf-8", "gb2312", $fileName);
        Header("Content-type:application/octet-stream ");
        Header("Accept-Ranges:bytes ");
        Header("Accept-Length:   " . filesize($filePath));
        Header("Content-Disposition:   attachment;   filename= " . $fileName);
        //   输出文件内容
        echo fread($file, filesize($filePath));
        fclose($file);
        exit;

    }

    /**
     * 质量管理文件预览
     * @return \think\response\Json
     */
    public function preview()
    {
        if (request()->isAjax()) {
            $param = input('post.');
            $type_model = input('param.type_model');//model类名
            $model = new $type_model();
            $code = 1;
            $msg = '预览成功';
            $data = $model->getOne($param['id']);
            //查询attachment文件上传表中的文件上传路径
            $attachment = Db::name("attachment")->where("id", $data["attachment_id"])->find();
            //上传文件路径
            $path = $attachment["filepath"];
            if (!$path || !file_exists("." . $path)) {
                return json(['code' => '-1', 'msg' => '文件不存在']);
            }
            $extension = strtolower(get_extension(substr($path, 1)));
            $pdf_path = './uploads/temp/' . basename($path) . '.pdf';
            if (!file_exists($pdf_path)) {
                if ($extension === 'doc' || $extension === 'docx' || $extension === 'txt') {
                    doc_to_pdf($path);
                } else if ($extension === 'xls' || $extension === 'xlsx') {
                    excel_to_pdf($path);
                } else if ($extension === 'ppt' || $extension === 'pptx') {
                    ppt_to_pdf($path);
                } else if ($extension === 'pdf') {
                    $pdf_path = $path;
                } else if ($extension === "jpg" || $extension === "png" || $extension === "jpeg") {
                    $pdf_path = $path;
                } else {
                    $code = 0;
                    $msg = '不支持的文件格式';
                }
                return json(['code' => $code, 'path' => substr($pdf_path, 1), 'msg' => $msg]);
            } else {
                return json(['code' => $code, 'path' => substr($pdf_path, 1), 'msg' => $msg]);
            }
        }
    }

    /**
     * 日常质量管理-现场图片
     * @param $draw
     * @param $table
     * @param $search
     * @param $start
     * @param $length
     * @param $limitFlag
     * @param $order
     * @param $columns
     * @param $columnString
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function quality_scene_picture($id, $draw, $table, $search, $start, $length, $limitFlag, $order, $columns, $columnString)
    {
        //查询
        //条件过滤后记录数 必要
        $recordsFiltered = 0;
        //表的总记录数 必要
        $year = input('year') ? input('year') : "";//年
        $month = input('month') ? input('month') : "";//月
        $day = input('day') ? input('day') : "";//日

        $admin_group_id = input('admin_group_id') ? input('admin_group_id') : "";
        if ($admin_group_id) {
            $group_data = [
                "admin_group_id" => $admin_group_id
            ];
        } else {
            $group_data = [
            ];
        }


        if (!$year && !$month && !$day)//如果年月日都不存在
        {
            $search_data = [
            ];
        } else if ($year && $month && $day)//如果年月日都存在
        {
            $search_data = [
                "year" => $year,
                "month" => $month,
                "day" => $day
            ];
        } else if ($year && !$month && !$day)//如果年都存在
        {
            $search_data = [
                "year" => $year
            ];
        } else if ($year && $month && !$day)//如果年月都存在
        {
            $search_data = [
                "year" => $year,
                "month" => $month
            ];
        }


        //表的总记录数 必要
        $recordsTotal = 0;
        $recordsTotal = Db::name($table)->where($search_data)->where($group_data)->where("admin_group_id > 0")->count(0);
        $recordsFilteredResult = array();
        if (strlen($search) > 0) {
            //有搜索条件的情况
            if ($limitFlag) {
                //*****多表查询join改这里******
                $recordsFilteredResult = Db::name($table)->field("filename,date,owner,company,position,id")->where($search_data)->where("admin_group_id > 0")->where($group_data)->where($columnString, 'like', '%' . $search . '%')->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = sizeof($recordsFilteredResult);
            }
        } else {
            //没有搜索条件的情况
            if ($limitFlag) {
                $recordsFilteredResult = Db::name($table)->field("filename,date,owner,company,position,id")->where($search_data)->where("admin_group_id > 0")->where($group_data)->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = $recordsTotal;
            }
        }

        $temp = array();
        $infos = array();
        foreach ($recordsFilteredResult as $key => $value) {
            //计算列长度
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
     * 日常质量管理-监理日志
     * @param $draw
     * @param $table
     * @param $search
     * @param $start
     * @param $length
     * @param $limitFlag
     * @param $order
     * @param $columns
     * @param $columnString
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function quality_supervision_log($id, $draw, $table, $search, $start, $length, $limitFlag, $order, $columns, $columnString)
    {
        //查询
        //条件过滤后记录数 必要
        $recordsFiltered = 0;
        //表的总记录数 必要
        $year = input('year') ? input('year') : "";//年
        $month = input('month') ? input('month') : "";//月
        $day = input('day') ? input('day') : "";//日

        if (!$year && !$month && !$day)//如果年月日都不存在
        {
            $search_data = [
            ];
        } else if ($year && $month && $day)//如果年月日都存在
        {
            $search_data = [
                "year" => $year,
                "month" => $month,
                "day" => $day
            ];
        } else if ($year && !$month && !$day)//如果年都存在
        {
            $search_data = [
                "year" => $year
            ];
        } else if ($year && $month && !$day)//如果年月都存在
        {
            $search_data = [
                "year" => $year,
                "month" => $month
            ];
        }


        //表的总记录数 必要
        $recordsTotal = 0;
        $recordsTotal = Db::name($table)->where($search_data)->where("admin_group_id > 0")->count(0);
        $recordsFilteredResult = array();
        if (strlen($search) > 0) {
            //有搜索条件的情况
            if ($limitFlag) {
                //*****多表查询join改这里******
                $recordsFilteredResult = Db::name($table)->field("filename,date,owner,company,position,id")->where($search_data)->where("admin_group_id > 0")->where($columnString, 'like', '%' . $search . '%')->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = sizeof($recordsFilteredResult);
            }
        } else {
            //没有搜索条件的情况
            if ($limitFlag) {
                $recordsFilteredResult = Db::name($table)->field("filename,date,owner,company,position,id")->where($search_data)->where("admin_group_id > 0")->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = $recordsTotal;
            }
        }

        $temp = array();
        $infos = array();
        foreach ($recordsFilteredResult as $key => $value) {
            //计算列长度
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
     * 日常质量管理-巡视记录
     * @param $draw
     * @param $table
     * @param $search
     * @param $start
     * @param $length
     * @param $limitFlag
     * @param $order
     * @param $columns
     * @param $columnString
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function quality_patrol_record($id, $draw, $table, $search, $start, $length, $limitFlag, $order, $columns, $columnString)
    {
        //查询
        //条件过滤后记录数 必要
        $recordsFiltered = 0;
        //表的总记录数 必要
        $year = input('year') ? input('year') : "";//年
        $month = input('month') ? input('month') : "";//月
        $day = input('day') ? input('day') : "";//日

        if (!$year && !$month && !$day)//如果年月日都不存在
        {
            $search_data = [
            ];
        } else if ($year && $month && $day)//如果年月日都存在
        {
            $search_data = [
                "year" => $year,
                "month" => $month,
                "day" => $day
            ];
        } else if ($year && !$month && !$day)//如果年都存在
        {
            $search_data = [
                "year" => $year
            ];
        } else if ($year && $month && !$day)//如果年月都存在
        {
            $search_data = [
                "year" => $year,
                "month" => $month
            ];
        }


        //表的总记录数 必要
        $recordsTotal = 0;
        $recordsTotal = Db::name($table)->where($search_data)->where("admin_group_id > 0")->count(0);
        $recordsFilteredResult = array();
        if (strlen($search) > 0) {
            //有搜索条件的情况
            if ($limitFlag) {
                //*****多表查询join改这里******
                $recordsFilteredResult = Db::name($table)->field("filename,date,owner,company,position,id")->where($search_data)->where("admin_group_id > 0")->where($columnString, 'like', '%' . $search . '%')->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = sizeof($recordsFilteredResult);
            }
        } else {
            //没有搜索条件的情况
            if ($limitFlag) {
                $recordsFilteredResult = Db::name($table)->field("filename,date,owner,company,position,id")->where($search_data)->where("admin_group_id > 0")->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = $recordsTotal;
            }
        }

        $temp = array();
        $infos = array();
        foreach ($recordsFilteredResult as $key => $value) {
            //计算列长度
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
     * 日常质量管理-旁站记录
     * @param $draw
     * @param $table
     * @param $search
     * @param $start
     * @param $length
     * @param $limitFlag
     * @param $order
     * @param $columns
     * @param $columnString
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function quality_side_reporting($id, $draw, $table, $search, $start, $length, $limitFlag, $order, $columns, $columnString)
    {
        //查询
        //条件过滤后记录数 必要
        $recordsFiltered = 0;
        //表的总记录数 必要
        $year = input('year') ? input('year') : "";//年
        $month = input('month') ? input('month') : "";//月
        $day = input('day') ? input('day') : "";//日

        if (!$year && !$month && !$day)//如果年月日都不存在
        {
            $search_data = [
            ];
        } else if ($year && $month && $day)//如果年月日都存在
        {
            $search_data = [
                "year" => $year,
                "month" => $month,
                "day" => $day
            ];
        } else if ($year && !$month && !$day)//如果年都存在
        {
            $search_data = [
                "year" => $year
            ];
        } else if ($year && $month && !$day)//如果年月都存在
        {
            $search_data = [
                "year" => $year,
                "month" => $month
            ];
        }


        //表的总记录数 必要
        $recordsTotal = 0;
        $recordsTotal = Db::name($table)->where($search_data)->where("admin_group_id > 0")->count(0);
        $recordsFilteredResult = array();
        if (strlen($search) > 0) {
            //有搜索条件的情况
            if ($limitFlag) {
                //*****多表查询join改这里******
                $recordsFilteredResult = Db::name($table)->field("filename,date,owner,company,position,id")->where($search_data)->where("admin_group_id > 0")->where($columnString, 'like', '%' . $search . '%')->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = sizeof($recordsFilteredResult);
            }
        } else {
            //没有搜索条件的情况
            if ($limitFlag) {
                $recordsFilteredResult = Db::name($table)->field("filename,date,owner,company,position,id")->where($search_data)->where("admin_group_id > 0")->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = $recordsTotal;
            }
        }

        $temp = array();
        $infos = array();
        foreach ($recordsFilteredResult as $key => $value) {
            //计算列长度
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
     * 日常质量管理-抽检记录
     * @param $draw
     * @param $table
     * @param $search
     * @param $start
     * @param $length
     * @param $limitFlag
     * @param $order
     * @param $columns
     * @param $columnString
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function quality_sampling($id, $draw, $table, $search, $start, $length, $limitFlag, $order, $columns, $columnString)
    {
        //查询
        //条件过滤后记录数 必要
        $recordsFiltered = 0;
        //表的总记录数 必要
        $year = input('year') ? input('year') : "";//年
        $month = input('month') ? input('month') : "";//月
        $day = input('day') ? input('day') : "";//日

        if (!$year && !$month && !$day)//如果年月日都不存在
        {
            $search_data = [
            ];
        } else if ($year && $month && $day)//如果年月日都存在
        {
            $search_data = [
                "year" => $year,
                "month" => $month,
                "day" => $day
            ];
        } else if ($year && !$month && !$day)//如果年都存在
        {
            $search_data = [
                "year" => $year
            ];
        } else if ($year && $month && !$day)//如果年月都存在
        {
            $search_data = [
                "year" => $year,
                "month" => $month
            ];
        }


        //表的总记录数 必要
        $recordsTotal = 0;
        $recordsTotal = Db::name($table)->where($search_data)->where("admin_group_id > 0")->count(0);
        $recordsFilteredResult = array();
        if (strlen($search) > 0) {
            //有搜索条件的情况
            if ($limitFlag) {
                //*****多表查询join改这里******
                $recordsFilteredResult = Db::name($table)->field("filename,date,owner,company,position,id")->where($search_data)->where("admin_group_id > 0")->where($columnString, 'like', '%' . $search . '%')->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = sizeof($recordsFilteredResult);
            }
        } else {
            //没有搜索条件的情况
            if ($limitFlag) {
                $recordsFilteredResult = Db::name($table)->field("filename,date,owner,company,position,id")->where($search_data)->where("admin_group_id > 0")->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = $recordsTotal;
            }
        }

        $temp = array();
        $infos = array();
        foreach ($recordsFilteredResult as $key => $value) {
            //计算列长度
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
     * 日常质量管理-监理指令
     * @param $draw
     * @param $table
     * @param $search
     * @param $start
     * @param $length
     * @param $limitFlag
     * @param $order
     * @param $columns
     * @param $columnString
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */

    public function quality_supervision_instruction($id, $draw, $table, $search, $start, $length, $limitFlag, $order, $columns, $columnString)
    {
        //查询
        //条件过滤后记录数 必要
        $recordsFiltered = 0;
        //表的总记录数 必要
        $year = input('year') ? input('year') : "";//年
        $month = input('month') ? input('month') : "";//月
        $day = input('day') ? input('day') : "";//日

        if (!$year && !$month && !$day)//如果年月日都不存在
        {
            $search_data = [
            ];
        } else if ($year && $month && $day)//如果年月日都存在
        {
            $search_data = [
                "year" => $year,
                "month" => $month,
                "day" => $day
            ];
        } else if ($year && !$month && !$day)//如果年都存在
        {
            $search_data = [
                "year" => $year
            ];
        } else if ($year && $month && !$day)//如果年月都存在
        {
            $search_data = [
                "year" => $year,
                "month" => $month
            ];
        }


        //表的总记录数 必要
        $recordsTotal = 0;
        $recordsTotal = Db::name($table)->where($search_data)->where("admin_group_id > 0")->count(0);
        $recordsFilteredResult = array();
        if (strlen($search) > 0) {
            //有搜索条件的情况
            if ($limitFlag) {
                //*****多表查询join改这里******
                $recordsFilteredResult = Db::name($table)->field("filename,date,owner,company,position,id")->where($search_data)->where("admin_group_id > 0")->where($columnString, 'like', '%' . $search . '%')->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = sizeof($recordsFilteredResult);
            }
        } else {
            //没有搜索条件的情况
            if ($limitFlag) {
                $recordsFilteredResult = Db::name($table)->field("filename,date,owner,company,position,id")->where($search_data)->where("admin_group_id > 0")->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = $recordsTotal;
            }
        }

        $temp = array();
        $infos = array();
        foreach ($recordsFilteredResult as $key => $value) {
            //计算列长度
            $length = sizeof($columns);
            for ($i = 0; $i < $length; $i++) {
                array_push($temp, $value[$columns[$i]['name']]);
            }
            $infos[] = $temp;
            $temp = [];

        }


        return json(['draw' => intval($draw), 'recordsTotal' => intval($recordsTotal), 'recordsFiltered' => $recordsFiltered, 'data' => $infos]);
    }


    // ht 单位质量管理 单位策划，单位管控 控制点列表
    public function unit_quality_control($id, $draw, $table, $search, $start, $length, $limitFlag, $order, $columns, $columnString)
    {
        if (!stristr($id, '-')) {
            return json(['draw' => intval($draw), 'recordsTotal' => intval(0), 'recordsFiltered' => 0, 'data' => '编号有误']);
        }
        // 前台 传递 id 的 时候 注意一下  把 左侧的 节点 add_id 和 当前 点击的 工序 编号 以 - 组合到一起
        $idArr = explode('-', $id);
        $division_id = $idArr[0]; // 这里存放 工程划分 单位工程编号
        $id = $idArr[1]; // 工序编号
        $table = 'controlpoint';
        //查询
        //条件过滤后记录数 必要
        $recordsFiltered = 0;
        $recordsFilteredResult = array();
        //表的总记录数 必要
        if ($id == 0) { // 等于0 说明是 作业 那就获取全部的 控制点 注意 这里不包含 单位策划里 追加的 关系数据
            $id = Db::name('materialtrackingdivision')->where(['type' => 3, 'cat' => 2])->column('id'); // 标准库单元工程下 所有的工序编号
            $recordsTotal = Db::name($table)->whereIn('procedureid', $id)->count();
            $new_control = '';
            $where_val = 'whereIn';
        } else {
            $recordsTotal = Db::name($table)->where('procedureid', $id)->count();
            // 合并 单位策划里 后来 添加的控制点
            // 注意 ：这里的控制点是
            // 存在于 quality_division_controlpoint_relation 单位质量管理 对应关系表里的 所以即使和 其他 工序下 的控制点重复也是正常的
            $new_control = Db::name('quality_division_controlpoint_relation')->where(['division_id' => $division_id, 'type' => 0, 'ma_division_id' => $id])->column('control_id');
            $recordsTotal = $recordsTotal + sizeof($new_control);
            $where_val = 'where';
        }
        if (strlen($search) > 0) {
            //有搜索条件的情况
            if ($limitFlag) {
                //*****多表查询join改这里******
                $recordsFilteredResult = Db::name($table)
                    ->field('code,name,id')
                    ->$where_val('procedureid', $id)
                    ->where($columnString, 'like', '%' . $search . '%')
                    ->whereOr('id', 'IN', $new_control)
                    ->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = sizeof($recordsFilteredResult);
            }
        } else {
            //没有搜索条件的情况
            if ($limitFlag) {
                //*****多表查询join改这里******
                $recordsFilteredResult = Db::name($table)
                    ->field('code,name,id')
                    ->$where_val('procedureid', $id)
                    ->whereOr('id', 'IN', $new_control)
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

    public function quality_division_controlpoint_relation($id, $draw, $table, $search, $start, $length, $limitFlag, $order, $columns, $columnString)
    {
        //查询
        //条件过滤后记录数 必要
        $recordsFiltered = 0;
        $recordsFilteredResult = array();
        $par = array();
        $par['type'] = 1;
        $par['division_id'] = $this->request->param('division_id');
        if ($this->request->has('ma_division_id')) {
            $par['ma_division_id'] = $this->request->param('ma_division_id');
        }
        //表的总记录数 必要
        $recordsTotal = Db::name($table)->where($par)->count();
        if (strlen($search) > 0) {
            //有搜索条件的情况
            if ($limitFlag) {
                //*****多表查询join改这里******
                $recordsFilteredResult = Db::name($table)->alias('a')
                    ->join('controlpoint b', 'a.control_id=b.id', 'left')
                    ->where($par)
                    ->order($order)->limit(intval($start), intval($length))->select();
                $recordsFiltered = sizeof($recordsFilteredResult);
            }
        } else {
            //没有搜索条件的情况
            if ($limitFlag) {
                //*****多表查询join改这里******
                $recordsFilteredResult = Db::name($table)->alias('a')
                    ->join('controlpoint b', 'a.control_id=b.id', 'left')
                    ->where($par)
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


}