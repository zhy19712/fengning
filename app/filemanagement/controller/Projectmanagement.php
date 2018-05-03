<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/5/2
 * Time: 15:06
 */
/**
 * 档案管理-工程项目管理
 * Class Projectmanagement
 * @package app\filemanagement\controller
 */
namespace app\filemanagement\controller;
use app\admin\controller\Permissions;
use app\filemanagement\model\ProjectmanagementModel;//档案管理-工程项目管理
use app\filemanagement\model\FilebranchtypeModel;//档案管理-分支目录管理-项目分类-树节点
use app\filemanagement\model\FilebranchModel;//档案管理-分支目录管理-项目分类
use think\exception\PDOException;
use think\Loader;
use think\Db;
use think\Request;
use think\Session;

class Projectmanagement extends Permissions
{
    /**
     * 模板首页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     *  获取一条信息
     * @return \think\response\Json
     */
    public function getindex()
    {
        if(request()->isAjax()){
            //实例化模型类
            $model = new ProjectmanagementModel();
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
            $model = new ProjectmanagementModel();
            $param = input('post.');

            //前台传过来的id
            if(empty($param['id']))//id为空时表示新增
            {
                $flag = $model->insertPro($param);
                return json($flag);
            }else{
                $flag = $model->editPro($param);
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
            $model = new ProjectmanagementModel();
            $param = input('post.');
            $flag = $model->delPro($param['id']);
            return $flag;
        }
    }

    /**
     * 获取组织机构下的机构名称
     * @return \think\response\Json
     */
    public function getGroup()
    {
        if(request()->isAjax()){
            $type = input('post.type');
            //type = 1,建设单位，type = 2,施工单位，type = 3,监理单位,type = 4,设计单位
            switch($type)
            {
                case 1:
                    $name = "建设单位";
                    break;
                case 2:
                    $name = "施工单位";
                    break;
                case 3:
                    $name = "监理单位";
                    break;
                case 4:
                    $name = "设计单位";
                    break;
            }
            $group_data = Db::name('admin_group_type')->alias('gt')
                ->join('admin_group g', 'g.type = gt.id', 'left')
                ->where("gt.name",$name)
                ->field("g.name")->order("g.id asc")->select();
            $result = array();
            foreach ((array)$group_data as $key=>$val)
            {
                $result[] = $val["name"];
            }
        }
        return (json(["data"=>$result]));
    }

    /**
     * 获取所有的项目类别
     * @return \think\response\Json
     */
    public function getBranchType()
    {
        //实例化模型类
        $model = new FilebranchtypeModel();
        $data = $model->getall();
        //定义一个空数组用来存放数据
        $result = array();
        foreach ($data as $key => $val)
        {
            if($val["id"] == 1)
            {
                unset($data[$key]);//去除最顶级的目录名
            }else
            {
                $result[] = $val["name"];
            }

        }
        return json(["data"=>$result]);
    }

    /**
     * 配置目录树
     * @return \think\response\Json
     */
    public function getBranchTree()
    {
            //实例化模型类
            $model = new FilebranchModel();
            //获取项目分类表中的所有的数据
            $data = $model->getDateAll();
            if(!empty($data))
            {
                //调取tree函数处理数据
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