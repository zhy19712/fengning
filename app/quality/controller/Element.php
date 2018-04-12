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
use think\Exception;
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
            {
                foreach ($mod['control_id'] as $item) {
                    $_item=array();
                    $_item['division_id'] = $mod['division_id'];
                    $_item['ma_division_id'] = $mod['ma_division_id'];
                    $_item['type'] = 1;
                    $_item['control_id'] = $item;
                    $_mod[] = $_item;
                }
                try {
                    $this->divisionControlPointService->allowField(true)->saveAll($_mod);
                } catch (Exception $e) {
                    return json(['code'=>-1,msg=>$e->getMessage()]);
                }
            }
            return json(['code' => 1]);
        }
        $this->assign('Division', $Division);
        $this->assign('TrackingDivision', $TrackingDivision);
        return $this->fetch();
    }

    /**
     * 单位管控
     * @return mixed
     */
    public
    function controll()
    {
        return $this->fetch();
    }

    /**
     * 单位验评
     * @return mixed
     */
    public
    function check()
    {
        return $this->fetch();
    }

    /**
     * 获取检验批列表
     * @param $id
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public
    function getDivisionUnitTree($id)
    {
        return json(DivisionUnitModel::all(['division_id' => $id]));
    }

    /**
     * 获取检验批列表
     * @param $id
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public
    function getProcedures($id)
    {
        return json(MaterialTrackingDivision::all(['pid' => $id, 'type' => 3]));
    }

    /**
     * 获取控制点
     * @param $Division 检验批
     * @param null $TrackingDivision 工序
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public
    function getControl($Division, $TrackingDivision = null)
    {
        $par['type'] = 1;
        $par['division_id'] = $Division;
        if (!empty($TrackingDivision)) {
            $par['ma_division_id'] = $TrackingDivision;
        }
        return json($this->divisionControlPointService->with('ControlPoint')->where($par));
    }
}