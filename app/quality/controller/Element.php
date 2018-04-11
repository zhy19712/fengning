<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/4/11
 * Time: 11:43
 */

namespace app\quality\controller;

use app\admin\controller\Permissions;

class Element extends Permissions
{
    /**
     * 单位策划
     * @return mixed
     */
    public function plan()
    {
        return $this->fetch();
    }

    /**
     * 单位管控
     * @return mixed
     */
    public function controll()
    {
        return $this->fetch();
    }

    /**
     * 单位验评
     * @return mixed
     */
    public function check()
    {
        return $this->fetch();
    }
}