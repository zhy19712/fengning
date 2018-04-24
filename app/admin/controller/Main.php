<?php
//主页，控制面板

namespace app\admin\controller;

use \think\Db;
use \think\Cookie;
use app\admin\controller\Permissions;

class Main extends Permissions
{
    public function index()
    {

        return $this->fetch();
    }

    public function selectperson()
    {
        return $this->fetch();
    }

}
