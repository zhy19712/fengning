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
}