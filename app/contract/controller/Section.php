<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/3/23
 * Time: 16:13
 */

namespace app\contract\controller;

use app\admin\controller\Permissions;

class Section extends Permissions
{
    public function index()
    {
        return $this->fetch();
    }
}