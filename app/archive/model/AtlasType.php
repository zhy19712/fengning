<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/29
 * Time: 9:34
 */
/*
 * 图纸文档管理，图册类型
 * @package app\archive\controller
 */
namespace app\admin\model;

use think\Db;
use \think\Model;

class AtlasType extends Model
{
    /*
     * 查询所有的角色类型表中的数据
     */

    public function getall()
    {
        return $this->select();
    }
}