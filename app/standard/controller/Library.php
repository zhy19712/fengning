<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/4/8
 * Time: 11:22
 */
namespace app\standard\controller;
use app\admin\controller\Permissions;

class Library extends Permissions
{
    public function index()
    {
        return $this->fetch();
    }
}