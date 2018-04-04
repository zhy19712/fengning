<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/4/4
 * Time: 15:45
 */
namespace app\standard\controller;
use app\admin\controller\Permissions;

class Templatemanage extends Permissions
{
    public function index()
    {
        return $this->fetch();
    }
}