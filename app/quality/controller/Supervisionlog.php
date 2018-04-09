<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/4/9
 * Time: 10:33
 */
namespace app\quality\controller;

use app\admin\controller\Permissions;
use app\admin\model\AdminGroup;//组织机构
use app\admin\model\Admin;//用户表
use app\quality\model\ScenePictureModel;//现场图片模型
use \think\Session;

/**
 * 日常质量管理，监理日志
 * Class Supervisionlog
 * @package app\quality\controller
 */
class Supervisionlog extends Permissions
{
    /**
     * 模板首页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    /**********************************监理日志类型树************************/
}