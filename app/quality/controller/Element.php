<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/4/11
 * Time: 11:43
 */

namespace app\quality\controller;

use app\admin\controller\Permissions;
use app\quality\model\DivisionUnitModel;

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

    public function addplan()
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

    /**
     * 获取检验批列表
     * @param $id
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getDivisionUnitTree($id)
    {
        return json(DivisionUnitModel::all(['division_id' => $id]));
    }
}