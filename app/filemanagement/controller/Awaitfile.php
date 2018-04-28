<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/4/28
 * Time: 10:48
 */
namespace  app\filemanagement\controller;
use app\admin\controller\Permissions;

class Awaitfile extends Permissions
{
    /**
     * 待整理文件
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 新增或编辑待整理文件
     */
    public function addoredit()
    {
        return $this->fetch();
    }
}