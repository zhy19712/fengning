<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/4/8
 * Time: 9:06
 */
namespace app\quality\controller;

use app\admin\controller\Permissions;
use app\admin\model\Admin as adminModel;//管理员模型
use app\admin\model\AdminGroup;//组织机构

use \think\Db;
use \think\Session;

/**
 * 日常质量管理，图片管理
 * Class Atlas
 * @package app\archive\controller
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

}