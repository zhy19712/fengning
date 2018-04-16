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
use app\quality\model\UploadModel;
use app\standard\model\ControlPoint;
use app\standard\model\MaterialTrackingDivision;
use think\Db;
use think\Exception;
use think\Request;

class Element extends Permissions
{
    protected $divisionControlPointService;
    protected $uploadService;

    public function __construct(Request $request = null)
    {
        $this->divisionControlPointService = new DivisionControlPointModel();
        $this->uploadService = new UploadModel();
        parent::__construct($request);
    }
##单元策划

    /**
     * 单元策划
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
    public function addplan($Division = null, $TrackingDivision = null)
    {
        if ($this->request->isAjax()) {
            $mod = input('post.');
            {
                foreach ($mod['control_id'] as $item) {
                    $_item = array();
                    $_item['division_id'] = $mod['division_id'];
                    $_item['ma_division_id'] = $mod['ma_division_id'];
                    $_item['type'] = 1;
                    $_item['control_id'] = $item;
                    $_mod[] = $_item;
                }
                try {
                    $this->divisionControlPointService->allowField(true)->saveAll($_mod);
                } catch (Exception $e) {
                    return json(['code' => -1, msg => $e->getMessage()]);
                }
            }
            return json(['code' => 1]);
        }
        $this->assign('Division', $Division);
        $this->assign('TrackingDivision', $TrackingDivision);
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
     * 获取工序列表
     * @param $id
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getProcedures($id)
    {
        return json(MaterialTrackingDivision::all(['pid' => $id, 'type' => 3]));
    }

    /**
     * 删除控制点
     * @param $id
     * @return \think\response\Json
     */
    public function delControlPointRelation($id)
    {
        $mod = DivisionControlPointModel::get($id);
        if ($mod['status'] == 1) {
            return json(['code' => -1, 'msg' => '控制点已执行']);
        }
        if ($mod->delete()) {
            return json(['code' => 1]);
        } else {
            return json(['code' => -1]);
        }
    }


##单元管控

    /**
     * 单位管控
     * @return mixed
     */
    public function controll()
    {
        return $this->fetch();
    }

    /**
     * 新增控制点执行情况
     * @param $cpr_id
     * @param $att_id
     * @return \think\response\Json
     */
    public function addExecution($cpr_id, $att_id)
    {
        $res = $this->uploadService->save(['contr_relation_id' => $cpr_id, 'attachment_id' => $att_id, 'type' => 1]);
        if ($res) {
            //更新控制点执行情况
            $this->divisionControlPointService->save(['status' => 1], ['id' => $cpr_id]);
            return json(['code' => 1]);
        } else {
            return json(['code' => -1]);
        }
    }

    /**
     * 新增图像资料
     * @param $cpr_id
     * @param $att_id
     * @return \think\response\Json
     */
    public function addAttData($cpr_id, $att_id)
    {
        $res = $this->uploadService->save(['contr_relation_id' => $cpr_id, 'attachment_id' => $att_id, 'type' => 2]);
        if ($res) {
            return json(['code' => 1]);
        } else {
            return json(['code' => -1]);
        }
    }


    ##单元验评

    /**
     * 单位验评
     * @return mixed
     */
    public function check()
    {
        return $this->fetch();
    }

    /**
     * 验评
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function Evaluate()
    {
        $mod = input('post.');
        $_mod = DivisionUnitModel::get($mod['Unit_id']);
        $_mod['EvaluateResult'] = $mod['EvaluateResult'];
        $_mod['EvaluateDate'] = $mod['EvaluateDate'];
        $res = $_mod->save();
        if ($res) {
            return json(['code' => 1]);
        } else {
            return json(['code' => -1]);
        }
    }
}