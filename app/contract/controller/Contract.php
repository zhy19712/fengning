<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/3/23
 * Time: 13:52
 */

namespace app\contract\controller;

use app\admin\controller\Permissions;

class Contract extends Permissions

{
    public function index()
    {
        return $this->fetch();
    }
}