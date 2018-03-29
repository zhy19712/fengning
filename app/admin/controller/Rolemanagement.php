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
use \think\Session;
use app\admin\model\AdminCateType;
use app\admin\model\Admin as adminModel;//管理员模型
use app\admin\model\AdminCate;
use app\admin\model\AdminGroup;

class Rolemanagement extends Permissions
{
    /*
     * 模板首页
     */
    public function index()
    {
        $current_name =Session::get('current_nickname');
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
            $res = tree($data);

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
        $flag2 = $user->deladmincate($param);
        $flag = $model->delCatetype($param['id']);
        $flag1 = $cate->delPidCate($param['id']);
        if($flag || $flag1 ||$flag2)
        {
            return json($flag);
        }

    }

    /*
     * 获取一条admin_cate表中的信息
     */
    public function getOne($id)
    {
        if(request()->isAjax()) {
            //实例化模型类
            $cate = new AdminCate();
            $param = input('post.');
            $data = $cate->getOne($param['id']);
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
                    'date' => $param['date'],//创建时间
                    'desc' => $param['desc']//备注

                ];
                $flag = $model->insertCate($data);
                return json($flag);
            }else{
                $data = [
                    'id' => $param['id'],
                    'number_id' => $param['number_id'],//编号
                    'role_name' => $param['role_name'],//角色名称
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
            //删除admin表中的admin_cate_id
            $flag1 = $user->deladmincate($param);
            // 最后删除此节点
            $flag = $model->delCate($param['id']);

            if ($res || $flag || $flag1) {
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
            //实例化模型类
            $user = new AdminModel();
            //先查询admin表中的所有的admin_cate_id
            $datainfo = $user->getAdmincateid();
            if($datainfo)
            {
                foreach ($datainfo as $k=>$v)
                {
                    //定义一个空数组
                    $res_info = array();
                    $admincateid = explode(",",$v['admin_cate_id']);
                    //判断传过来的admin_cate_id中的id是否存在admin表中的admin_cate_id字段中
                    if(in_array($param["id"],$admincateid))
                    {
                        $res_info["id"] = $v["id"];
                        $res_info["name"] = $v["name"];
                        $data = $res_info;
                        $res[]=$data;

                    }
                }

                return $res;//返回json数据
            }else
            {
                return $this->fetch();
            }

        }
    }

    /**
     * 根据角色类型删除角色类型下的用户
     * @return \think\response\Json
     */
    public function delAdminname()
    {
        if(request()->isAjax()) {
            $param = input('post.');
            //实例化model类型
            $model = new AdminModel();

            $flag = $model->deladmincateid($param);

            return $flag;

        }else
        {
            return $this->fetch();
        }
    }

    /*
     * 弹框添加角色类型下的分组用户模板
     */
    public function addpeople()
    {
        return $this->fetch();
    }

    /**
     * 添加角色类型下的分组用户
     * @return \think\response\Json
     */
    public function addAdminname()
    {

        if(request()->isAjax()) {
            $model = new AdminModel();
            $param = input('post.');//需要前台传过来用户数组admin_id,cate表中的id
            $data = $model->insertAdminid($param);
            return json($data);
        }
    }

    /**
     * 获取 组织机构 左侧的树结构
     * @return mixed|\think\response\Json
     */
    public function getindex()
    {

        if(request()->isAjax()) {
            // 获取左侧的树结构
            $model = new AdminGroup();
            //定义一个空的字符串
            $str = "";
            $data = $model->getall();
            $res = tree($data);

            foreach ((array)$res as $k => $v) {

                $v['id'] = strval($v['id']);
                $v['pid'] = strval($v['pid']);
                $res[$k] = json_encode($v);
            }

            $user = Db::name('admin')->field('id,admin_group_id,name')->select();
            if(!empty($user))//如果$user不为空时
            {
                foreach((array)$user as $key=>$vo){
                    $id = $vo['id'] + 10000;
                    $str .= '{ "id": "' . $id . '", "pid":"' . $vo['admin_group_id'] . '", "name":"' . $vo['name'].'"';
                    $str .= '}*';
                }
                $str = substr($str, 0, -1);

                $str = explode("*",$str);

                //$res,$str这两个数组都存在时，才可以合并

                if($res && $str)
                {
                    $merge = array_merge($res,$str);
                }

                return json($merge);
            }

        }
    }

    /**
     * 管理员角色添加和修改操作
     * @return mixed|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function catepublish()
    {
        //获取角色id
        $roleId = $this->request->has('roleId') ? $this->request->param('roleId', 0, 'intval') : 0;

        $id = $this->request->has('id') ? $this->request->param('id', 0, 'intval') : 0;

        $id = $roleId?$roleId:$id;

        $model = new \app\admin\model\AdminCate();

        if($id > 0) {

            //是修改操作
            if(request()->isAjax()) {
                //是提交操作
                $post = $this->request->post();
                //验证  唯一规则： 表名，字段名，排除主键值，主键名
                //验证用户名是否存在
                //处理选中的权限菜单id，转为字符串
                if(!empty($post['admin_menu_id'])) {
                    $post['permissions'] = implode(',',$post['admin_menu_id']);
                } else {
                    $post['permissions'] = '0';
                }
                if(false == $model->allowField(true)->save($post,['id'=>$id])) {
                    return $this->error('修改失败');
                } else {

                    addlog($model->id);//写入日志

                    return $this->success('修改角色信息成功');
                }
            } else {
                //非提交操作
                $info['cate'] = $model->where('id',$id)->find();
                if(!empty($info['cate']['permissions'])) {
                    //将菜单id字符串拆分成数组
                    $info['cate']['permissions'] = explode(',',$info['cate']['permissions']);
                }
                $menus = Db::name('admin_menu')->select();
                $info['menu'] = $this->menulist($menus);
                $this->assign('info',$info);
                return $this->fetch();
            }
        }else{
            $menus = Db::name('admin_menu')->select();
            $info['menu'] = $this->menulist($menus);
            $this->assign('info',$info);
            return $this->fetch();
        }
    }

    protected function menulist($menu,$id=0,$level=0){

        static $menus = array();
        $size = count($menus)-1;
        foreach ($menu as $value) {
            if ($value['pid']==$id) {
                $value['level'] = $level+1;
                if($level == 0)
                {
                    $value['str'] = str_repeat('',$value['level']);
                    $menus[] = $value;
                }
                elseif($level == 2)
                {
                    $value['str'] = '&emsp;&emsp;&emsp;&emsp;'.'└ ';
                    $menus[$size]['list'][] = $value;
                }
                elseif($level == 3)
                {
                    $value['str'] = '&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;'.'└ ';
                    $menus[$size]['list'][] = $value;
                }
                else
                {
                    $value['str'] = '&emsp;&emsp;'.'└ ';
                    $menus[$size]['list'][] = $value;
                }

                $this->menulist($menu,$value['id'],$value['level']);
            }
        }
        return $menus;
    }

}

