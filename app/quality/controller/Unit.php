<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/3/27
 * Time: 16:24
 */
namespace app\quality\controller;
use app\admin\controller\Permissions;

class Unit extends Permissions{
    public  function plan()
    {
        return $this->fetch();
    }
    public  function control()
    {
        return $this->fetch();
    }
}