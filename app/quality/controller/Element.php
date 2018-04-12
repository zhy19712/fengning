<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/4/11
 * Time: 11:43
 */

namespace app\quality\controller;

use app\admin\controller\Permissions;
use app\quality\model\DivisionControlPointModel;
use app\quality\model\DivisionUnitModel;
use app\standard\model\ControlPoint;
use app\standard\model\MaterialTrackingDivision;
use think\Db;
use think\Request;

class Element extends Permissions
{
    protected $divisionControlPointService;

    public function __construct(Request $request = null)
    {
        $this->divisionControlPointService = new DivisionControlPointModel();
        parent::__construct($request);
    }

    /**
     * 单位策划
     * @return mixed
     */
    public function plan()
    {
        return $this->fetch();
    }

    /**
     * 新增控制点
     * @param $Division 划分树
     * @param $TrackingDivision 工序
     * @return mixed
     */
    public function addplan($Division, $TrackingDivision)
    {
        if ($this->request->isAjax()) {
            $mod = input('post.');
            $res = $this->divisionControlPointService->allowField(true)->save($mod);
            if ($res) {
                return json(['code' => 1]);
            } else {
                return json(['code' => -1]);
            }
        }
        $this->assign('Division', $Division);
        $this->assign('TrackingDivision', $TrackingDivision);
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
}