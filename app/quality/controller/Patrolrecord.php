<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/4/9
 * Time: 11:44
 */
/**
 * 日常质量管理，巡视记录
 * Class Patrolrecord
 * @package app\quality\controller
 */
namespace app\quality\controller;
use app\admin\controller\Permissions;
use app\admin\model\AdminGroup;//组织机构
use app\admin\model\Admin;//用户表
use app\quality\model\PatrolRecordModel;//巡视记录模型
use \think\Session;

class Patrolrecord extends Permissions
{
    /**
     * 模板首页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    /**********************************巡视记录类型树************************/
    /**
     * 巡视记录类型树
     * @return mixed|\think\response\Json
     */
    public function tree()
    {
        if ($this->request->isAjax()) {
            //实例化模型
            $model = new PatrolRecordModel();
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
    /**********************************巡视记录************************/
    /**
     * 获取一条巡视记录信息
     */
    public function getindex()
    {
        if(request()->isAjax()){
            $model = new PatrolRecordModel();
            $param = input('post.');
            $data = $model->getOne($param['id']);
            return json(['code'=> 1, 'data' => $data]);
        }
        return $this->fetch();
    }

    /**
     * 上传巡视记录
     * @return \think\response\Json
     */
    public function add()
    {
        if(request()->isAjax()){
            //实例化模型类
            $model = new PatrolRecordModel();
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
                $model -> insertPatrol($data);

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
                $model -> insertPatrol($data1);

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
                    "filename" => date("YmdHis"),//默认上传的文件名为日期
                    "date" => date("Y-m-d H:i:s"),
                    "owner" => Session::get('current_name'),
                    "company" => $group["name"],//单位
                    "admin_group_id" => $admininfo["admin_group_id"],
                    "path" => $param['path']//路径
                ];
                $flag = $model->insertPatrol($data2);
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
                    $model -> insertPatrol($data1);
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
                        "filename" => date("YmdHis"),//默认上传的文件名为日期
                        "date" => date("Y-m-d H:i:s"),
                        "owner" => Session::get('current_name'),
                        "company" => $group["name"],//单位
                        "admin_group_id" => $admininfo["admin_group_id"],
                        "path" => $param['path']//路径
                    ];
                    $flag = $model->insertPatrol($data2);
                    return json($flag);

                }else{
                    //3.如果当前的年份、月份都存在时，新增完整的一条现场图片信息
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
                        "filename" => date("YmdHis"),//默认上传的文件名为日期
                        "date" => date("Y-m-d H:i:s"),
                        "owner" => Session::get('current_name'),
                        "company" => $group["name"],//单位
                        "admin_group_id" => $admininfo["admin_group_id"],
                        "path" => $param['path']//路径
                    ];
                    $flag = $model->insertPatrol($data);
                    return json($flag);
                }
            }
        }
    }

    /**
     * 编辑一条巡视记录信息
     */
    public function edit()
    {
        if(request()->isAjax()){
            $model = new PatrolRecordModel();
            $param = input('post.');
            $data = [
                'id' => $param['id'],//巡视记录自增id
                'filename' => $param['filename']//上传文件名
            ];
            $flag = $model->editPatrol($data);
            return json($flag);

        }
    }

    /**
     * 下载一条巡视记录信息
     * @return \think\response\Json
     */
    public function download()
    {
        if(request()->isAjax()){
            $id = input('param.id');
            $model = new PatrolRecordModel();
            $param = $model->getOne($id);
            if(!$param['path'] || !file_exists("." .$param['path'])){
                return json(['code' => '-1','msg' => '文件不存在']);
            }
            return json(['code' => 1]);
        }
        $id = input('param.id');
        $model = new PatrolRecordModel();
        $param = $model->getOne($id);

        $filePath = '.' . $param['path'];
        $fileName = $param['filename'];
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

    /**
     * 删除一条巡视记录信息
     */
    public function del()
    {

        if (request()->isAjax()) {

            $id = input('post.id');//要删除的巡视记录id

            //实例化model类型
            $model = new PatrolRecordModel();
            //首先判断一下删除的只一天所属的月份下是否有其他日子
            $data_info = $model->getOne($id);

            $day_count = $model->getcount($data_info['pid']);

            $data_month = $model->getOne($data_info["pid"]);

            //如果一个月份下只有一条的话就删除这个月份
            if($day_count < 2)
            {
                $model -> delPatrol($data_info['pid']);

            }

            //判断年份下只有一条的话就删除这个年份
            $year_count = $model->getcount($data_month['pid']);
            if($year_count < 1)
            {
                //如果一个年份下只有一条的话就删除这个年份
                $model -> delPatrol($data_month['pid']);
            }

            //最后删除这条巡视记录信息
            $path = "." .$data_info['path'];
            $pdf_path = './uploads/temp/' . basename($path) . '.pdf';
            if(file_exists($path)){
                unlink($path); //删除上传的图片或文件
            }
            if(file_exists($pdf_path)){
                unlink($pdf_path); //删除生成的预览pdf
            }

            //最后删除这一条记录信息
            $flag = $model->delPatrol($id);
            return $flag;

        }
    }

    /**
     * 预览一条巡视记录信息
     * @return \think\response\Json
     */
    public function preview()
    {
        $model = new PatrolRecordModel();
        if(request()->isAjax()) {
            $param = input('post.');
            $code = 1;
            $msg = '预览成功';
            $data = $model->getOne($param['id']);
            if(!$data['path'] || !file_exists("." .$data['path'])){
                return json(['code' => '-1','msg' => '文件不存在']);
            }
            $path = $data['path'];
            $extension = strtolower(get_extension(substr($path,1)));
            $pdf_path = './uploads/temp/' . basename($path) . '.pdf';
            if(!file_exists($pdf_path)){
                if($extension === 'doc' || $extension === 'docx' || $extension === 'txt'){
                    doc_to_pdf($path);
                }else if($extension === 'xls' || $extension === 'xlsx'){
                    excel_to_pdf($path);
                }else if($extension === 'ppt' || $extension === 'pptx'){
                    ppt_to_pdf($path);
                }else if($extension === 'pdf'){
                    $pdf_path = $path;
                }else if($extension === "jpg" || $extension === "png" || $extension === "jpeg"){
                    $pdf_path = $path;
                }else {
                    $code = 0;
                    $msg = '不支持的文件格式';
                }
                return json(['code' => $code, 'path' => substr($pdf_path,1), 'msg' => $msg]);
            }else{
                return json(['code' => $code,  'path' => substr($pdf_path,1), 'msg' => $msg]);
            }
        }
    }
}