<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/4/8
 * Time: 9:06
 */
namespace app\quality\controller;

use app\admin\controller\Permissions;
use app\admin\model\AdminGroup;//组织机构
use app\admin\model\Admin;//用户表
use app\quality\model\ScenePictureModel;//现场图片模型

use \think\Db;
use \think\Session;

/**
 * 日常质量管理，图片管理
 * Class Atlas
 * @package app\quality\controller
 */
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

            foreach ((array)$res as $k => $v) {
                $v['id'] = strval($v['id']);
                $res[$k] = json_encode($v);
            }

            return json($res);
        }
    }

    /**********************************现场图片类型树************************/
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

            //获取用户id
            $admin_id = Session::get('current_id');
            //根据用户id查询对应的组织机构的admin_group_id
            $admininfo = $admin->getadmininfo($admin_id);
            //获取组织机构id
            $admin_group_id = $admininfo["admin_group_id"];

            //获取当前时间的年月日
            $year = date("Y");
            $month = date("m");
            $day = date("d");

            //根据当前的上传时间获取父级节点id
            //1.查询当前的年份是否存在,如果年份不存在时，新建一条年份记录
            $search_info =[
                "year" => $year
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
            }else{
                //2.查询当前的月份是否存在,如果月份不存在时，新建一条月份记录
                $search_info =[
                    "year" => $year,
                    "month" => $month
                ];
                $result1 = $model->getid($search_info);
                if(!$result1['id'])//如果当前的月份不存在就新建当前月的记录
                {
                    $data = [
                        "year" => $year,
                        "month" => $month,
                        "name" => $month."月",
                        'pid' => $result['id']
                    ];
                    //新增一条月份
                    $model -> insertScene($data);
                }else{
                    //3.如果当前的年份、月份都存在时，新增完整的一条现场图片信息
                    //查询当前登录的用户所属的组织机构名
                    $admininfo = $admin->getadmininfo(Session::get('current_id'));
                    $group = $group->getOne($admininfo["admin_group_id"]);
                    $data = [
                        "year" => $year,
                        "month" => $month,
                        "day" => $day,
                        "name" => $day."日",
                        "pid" => $result1['id'],
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

}