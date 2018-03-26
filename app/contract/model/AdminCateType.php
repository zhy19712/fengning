<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/3/26
 * Time: 10:19
 */
namespace app\contract\model;

use think\Model;
use think\Session;

class AdminCateType extends Model
{

    /*
     * 查询所有的角色类型表中的数据
     */

    public function getall()
    {
        return $this->select();
    }
}