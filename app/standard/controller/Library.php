<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/4/8
 * Time: 11:22
 */

namespace app\standard\controller;

use app\admin\controller\Permissions;

/**
 * 标准库
 * Class Library
 * @package app\standard\controller
 */
class Library extends Permissions
{
    public function index()
    {
        return $this->fetch();
    }

    public function addcontrollpoint()
    {
        return $this->fetch();
    }
}