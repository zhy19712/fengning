<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/23
 * Time: 15:05
 */
/*
 * 角色管理
 */
namespace app\admin\controller;

use \think\Db;
use \think\Cookie;
use \think\Session;
use app\admin\controller\Permissions;
use app\admin\model\AdminCateType;
use app\admin\model\Admin as adminModel;//管理员模型
use app\admin\model\AdminCate;

class Rolemanagement extends Permissions
{
     /**
     * 角色分类树function
     * @return [type] [description]
     */
    function tree($data,$pid=0){
        static $treeList = array();
        foreach($data as $v){
            if($v['pid']==$pid){
                $treeList[]=$v;//将结果装到$treeList中
                $this->tree($data,$v['id']);
            }
        }
        return $treeList;
    }

    /*
     * 模板首页
     */
    public function index()
    {
        $current_name =Session::get('current_name');
        $this->assign("current_name",$current_name);
        return $this->fetch();
    }

    /*
     * 角色分类树
     * @return mixed|\think\response\Json
     */
    public function roletree()
    {
        if ($this->request->isAjax()) {
            //实例化角色类型AdminCateType
            $model = new AdminCateType();
            //查询fengning_admin_cate_type角色类型表
            $data = $model->getall();
            $res = $this->tree($data);

        foreach ((array)$res as $k => $v) {
            $v['id'] = strval($v['id']);
            $res[$k] = json_encode($v);
        }

            return json($res);
        }

    }

    /**
     * 新增 或者 编辑 角色类型的节点树
     * @return mixed|\think\response\Json
     */
    public function editCatetype()
    {
        if(request()->isAjax()){
            $model = new AdminCateType();
            $param = input('post.');
            /**
             * 前台需要传递的是 pid 父级节点编号,id自增id,name节点名称
             */

            if(empty($param['id']))//id为空时表示新增角色类型节点
            {
                $data = [
                    'pid' => $param['pid'],
                    'name' => $param['name']
                ];
                $flag = $model->insertCatetype($data);
                return json($flag);
            }else{
                $data = [
                    'id' => $param['id'],
                    'name' => $param['name']
                ];
                $flag = $model->editCatetype($data);
                return json($flag);
            }
        }
        return $this->fetch();
    }

    /**
     * 删除角色类型的节点树
     * @return \think\response\Json
     */
    public function delCatetype()
    {
        $param = input('post.');
        $model = new AdminCateType();
        // 先删除节点下的用户
        $user = new AdminModel();
        $cate = new AdminCate();
        $data = $cate->findcateid($param['id']);

        if(!empty($data))
        {
            foreach ((array)$data as $v)
            {
                $user->delUserByCateId($v);//循环删除同个admin_cate_id下的用户
            }
        }

        // 最后删除此节点
        $flag = $model->delCatetype($param['id']);
        return json($flag);
    }
    /*
     * 获取一条admin_cate表中的信息
     */
    public function getOne($id)
    {
        if(request()->isAjax()) {
            //实例化模型类
            $cate = new AdminCate();
            $data = $cate->getOne($id);
            return json(['code'=> 1, 'data' => $data]);
        }else
        {
            return $this->fetch();
        }

    }

    /**
     * 新增 或者 编辑 角色类型
     * @return mixed|\think\response\Json
     */
    public function editCate()
    {
        if(request()->isAjax()){
            $model = new AdminCate();
            $param = input('post.');
            //前台传过来的角色类型id
            if(empty($param['id']))//id为空时表示新增角色类型节点
            {
                $data = [
                    'pid' => $param['pid'],//admin_cate_type表中的id_
                    'number_id' => $param['number_id'],//编号
                    'role_name' => $param['role_name'],//角色名称
                    'create_owner' => $param['create_owner'],//创建人
                    'create_time' => $param['create_time'],//创建时间
                    'desc' => $param['desc']//备注

                ];
                $flag = $model->insertCate($data);
                return json($flag);
            }else{
                $data = [
                    'id' => $param['id'],
                    'number_id' => $param['number_id'],//编号
                    'role_name' => $param['role_name'],//角色名称
                    'create_owner' => $param['create_owner'],//创建人
                    'create_time' => $param['create_time'],//创建时间
                    'desc' => $param['desc']//备注

                ];
                $flag = $model->editCate($data);
                return json($flag);
            }
        }
        return $this->fetch();
    }

    /**
     * 删除角色类型
     * @return \think\response\Json
     */
    public function delCate()
    {
        if(request()->isAjax()) {
            $param = input('post.');
            $model = new AdminCate();
            // 先删除节点下的用户
            $user = new AdminModel();
            $res = $user->delUserByCateId($param['id']);
            // 最后删除此节点
            $flag = $model->delCate($param['id']);

            if ($res || $flag) {
                return json($flag);
            }
        }

    }

    /**
     * 根据角色类型查询角色类型下的所有用户
     * @return \think\response\Json
     */

    public function getAdminname()
    {

        if(request()->isAjax()) {

            $param = input('post.');
            //定义一个空数组
            $res = array();
            $where = array();
            if($param['name'])
            {
                $where['name'] = $param['name'];//搜索用户名
            }

            //实例化模型类
            $model = new AdminCate();
            $user = new AdminModel();
            //先查询所有的admin_id
            $all_admin_id = $model->getAdminid($param['id']);

            if($all_admin_id)
            {
                $all_admin_id = explode(",",$all_admin_id['admin_id']);//拆分字符串

                foreach ((array)$all_admin_id as $v)
                {
                    $where['id'] = $v;
                    $res[] = $user->getName($where);
                }
                halt($res);
            }
            $this->assign('admininfo',$res);
            return $this->fetch();
        }
    }

    /**
     * 删除角色类型下的分组用户
     * @return \think\response\Json
     */

    public function delAdminname()
    {
        $model = new AdminCate();
        $param = input('post.');
        $param['id'] = 20;
        $param['admin_id'] = 22;
        $data = $model->delAdminid($param);
//        halt($data);
    }

}

