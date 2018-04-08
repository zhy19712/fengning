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
//        if(request()->isAjax()){
            $model = new ScenePictureModel();
            $admin = new Admin();
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
            //1.查询当前的年份是否存在
            $result = $model->getyear($year);
            halt($result["id"]);





//            $data = [
//                'pid' => $id,//pid为父级id
//
//                'picture_number' => $param['picture_number'],//图号
//                'picture_name' => $param['picture_name'],//图名
//                'picture_papaer_num' => 1,//图纸张数(输入数字),默认1
//                'completion_date' => date("Y-m"),//完成日期
//                'paper_category' => $info['paper_category'],//图纸类别
//                'owner' => Session::get('current_nickname'),//上传人
//                'path' => $param['path'],//图片路径
//                'filename' => $param['filename'],
//                'date' => date("Y-m-d")//上传日期
//            ];
//            $flag = $model->insertCate($data);
//            return json($flag);
//        }
    }

}