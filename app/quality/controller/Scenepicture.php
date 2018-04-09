<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/4/8
 * Time: 9:06
 */
/**
 * 日常质量管理，图片管理
 * Class Scenepicture
 * @package app\quality\controller
 */
namespace app\quality\controller;
use app\admin\controller\Permissions;
use app\admin\model\AdminGroup;//组织机构
use app\admin\model\Admin;//用户表
use app\quality\model\ScenePictureModel;//现场图片模型
use \think\Session;

class Scenepicture extends Permissions
{
    /**
     * 模板首页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    /**********************************现场图片类型树************************/
    /**
     * 现场图片类型树
     * @return mixed|\think\response\Json
     */
    public function picturetree()
    {
        if ($this->request->isAjax()) {
            //实例化模型ScenePictureModel
            $model = new ScenePictureModel();
            //查询fengning_scene_picture现场图片表
            $data = $model->getall();
            $res = tree($data);

            foreach ((array)$res as $a => $b) {
                $b['id'] = strval($b['id']);
                $res[$a] = json_encode($b);
            }

            return json($res);
        }
    }

    /**********************************现场图片************************/
    /**
     * 获取一条现场图片信息
     */
    public function getindex()
    {
        if(request()->isAjax()){
            $model = new ScenePictureModel();
            $param = input('post.');
            $data = $model->getOne($param['id']);
            return json(['code'=> 1, 'data' => $data]);
        }
        return $this->fetch();
    }

    /**
     * 获取所有的组织机构
     */
    public function getAllgroup()
    {
        //实例化模型类
        $model = new AdminGroup();
        $data = $model->getfengning();
        if($data)
        {
            return json(['code'=>1,'data'=>$data]);
        }else
        {
            return json(['code'=>-1,'data'=>""]);
        }

    }

    /**
     * 上传现场图片
     * @return \think\response\Json
     */
    public function addPicture()
    {
        if(request()->isAjax()){
            //实例化模型类
            $model = new ScenePictureModel();
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
                $model -> insertScene($data);

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
                $model -> insertScene($data1);

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
                $flag = $model->insertScene($data2);
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
                    $model -> insertScene($data1);
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
                    $flag = $model->insertScene($data2);
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
                    $flag = $model->insertScene($data);
                    return json($flag);
                }
            }
        }
    }

    /**
     * 编辑一条现场图片信息
     */
    public function editPicture()
    {
        if(request()->isAjax()){
            $model = new ScenePictureModel();
            $param = input('post.');
                $data = [
                    'id' => $param['id'],//现场图片自增id
                    'filename' => $param['filename']//上传文件名
                ];
                $flag = $model->editScene($data);
                return json($flag);

        }
    }

    /**
     * 下载一条现场图片信息
     * @return \think\response\Json
     */
    public function downloadPicture()
    {
        if(request()->isAjax()){
            $id = input('param.id');
            $model = new ScenePictureModel();
            $param = $model->getOne($id);
            if(!$param['path'] || !file_exists("." .$param['path'])){
                return json(['code' => '-1','msg' => '文件不存在']);
            }
            return json(['code' => 1]);
        }
        $id = input('param.id');
        $model = new ScenePictureModel();
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
     * 删除一条现场图片信息
     */
    public function delPicture()
    {

        if (request()->isAjax()) {

            $id = input('post.id');//要删除的现场图片id

            //实例化model类型
            $model = new ScenePictureModel();
            //首先判断一下删除的只一天所属的月份下是否有其他日子
            $data_info = $model->getOne($id);

            $day_count = $model->getcount($data_info['pid']);

            $data_month = $model->getOne($data_info["pid"]);

            //如果一个月份下只有一条的话就删除这个月份
            if($day_count < 2)
            {
                $model -> delScene($data_info['pid']);

            }

            //判断年份下只有一条的话就删除这个年份
            $year_count = $model->getcount($data_month['pid']);
            if($year_count < 1)
            {
                //如果一个年份下只有一条的话就删除这个年份
                $model -> delScene($data_month['pid']);
            }

            //最后删除这条现场图片信息
            $path = "." .$data_info['path'];
            $pdf_path = './uploads/temp/' . basename($path) . '.pdf';
            if(file_exists($path)){
                unlink($path); //删除上传的图片或文件
            }
            if(file_exists($pdf_path)){
                unlink($pdf_path); //删除生成的预览pdf
            }

            //最后删除这一条记录信息
            $flag = $model->delScene($id);
            return $flag;

        }
    }

    /**
     * 预览一条现场图片信息
     * @return \think\response\Json
     */
    public function previewPicture()
    {
        $model = new ScenePictureModel();
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