<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/4/13
 * Time: 11:26
 */
/**
 * 日常质量管理，图片管理
 * Class Scenepicture
 * @package app\quality\controller
 */
namespace app\quality\controller;
use app\admin\controller\Permissions;
use \think\Session;
use think\Db;

class Branch extends Permissions
{
    public function plan()
    {
        return $this->fetch();

    }

    public function control()
    {
        return $this->fetch();

    }
}