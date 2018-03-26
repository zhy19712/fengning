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

class Rolemanagement extends Permissions
{
     /**
     * 角色分类树
     * @return [type] [description]
     */
    function tree($data,$pid=0,$level = 1){
        static $treeList = array();
        foreach($data as $v){
            if($v['pid']==$pid){
                $v['level']=$level;
                $treeList[]=$v;//将结果装到$treeList中
                $this->tree($data,$v['id'],$level+1);
            }
        }
        return $treeList;
    }

    /*
     * 模板首页
     */
    public function index()
    {
        return $this->fetch();
    }

    /*
     * 角色分类树
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
            $v['level'] = strval($v['level']);
            $res[$k] = json_encode($v);
        }

            return json($res);
        }

    }

}

