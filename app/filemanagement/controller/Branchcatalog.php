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
}