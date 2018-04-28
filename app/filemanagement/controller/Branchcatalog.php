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
        $data = $model->getAll($classifyid);
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
                    "class_name" => $param["class_name"],
                    "pid" => $param["pid"]
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
}