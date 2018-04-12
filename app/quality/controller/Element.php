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
use app\standard\model\ControlPoint;
use app\standard\model\MaterialTrackingDivision;
use think\Db;

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

    /**
     * 获取检验批列表
     * @param $id
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getProcedures($id)
    {
        return json(MaterialTrackingDivision::all(['pid' => $id, 'type' => 3]));
    }

    /**
     * 获取控制点
     * @param $id 工序Id
     */
    public function getControlPointsByProcedureId($id)
    {
        return json(ControlPoint::all(['procedureid' => $id]));
    }

    /**
     * 获取控制点
     * @param $id 检验批Id
     */
    public function getCPByDivisionId($id)
    {
        $ids[] = Db::name('fengning_materialtrackingdivision')->where(['pid' => $id])->field('id');
        return json(Db::name('controlpoint')->whereIn('procedureid', $ids));
    }
}