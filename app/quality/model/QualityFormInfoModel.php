<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/4/18
 * Time: 13:48
 */

namespace app\quality\model;

use app\admin\model\Admin;
use app\approve\model\IApprove;
use think\Exception;
use think\Model;

class QualityFormInfoModel extends Model implements IApprove
{
    protected $name = 'quality_form_info';
    protected $autoWriteTimestamp = true;

    public function CurrentApprover()
    {
        return $this->hasOne('app\admin\model\Admin', 'id', 'CurrentApproverId');
    }

    /**
     * 提交审批业务关联逻辑
     * @param $dataId
     * @param \app\approve\model\审批人id串 $approverIds
     * @param \app\approve\model\当前审批人Id $currentApproverId
     * @param int $currentStep
     * @param int $approveStatus
     * @return mixed|void
     */
    public function SubmitHandle($dataId, $approverIds, $currentApproverId, $currentStep = 1, $approveStatus = 1)
    {
        $this->save(['ApproveIds' => $approverIds, 'CurrentApproverId' => $currentApproverId, 'CurrentStep' => $currentStep, 'ApproveStatus' => $approveStatus], ['id' => $dataId]);
    }

    /**
     * 获取常用审批人列表
     * @param \app\approve\model\审批提交人Id $user_id
     * @return mixed|void
     */
    public function FrequentlyUsedApprover($user_id)
    {
        $userlist = self::where(['user_id' => $user_id])->whereNotNull('ApproveIds', 'and')->field('ApproveIds')->select();
        $ids = array();
        foreach ($userlist as $item) {
            $_ids = explode(",", $item['ApproveIds']);
            foreach ($_ids as $_id) {
                if (!in_array($_id, $ids)) {
                    $ids[] = $_id;
                }
            }
        }
        $users = array();
        if (sizeof($ids) > 0) {
            $adminService = new Admin();
            $users = $adminService->whereIn('id', $ids)->field('id,nickname')->select();
        }
        return $users;
    }

    /**
     * 获取业务审批基本信息
     * @param $dataId
     * @return array|false|mixed|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function GetApproveInfo($dataId)
    {
        try {
            $mod = self::where(['id' => $dataId])->field('user_id,ApproveIds,CurrentApproveId,CurrentStep')->find();
            return $mod;
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * 表单数据完整性检测
     * @param $dataId
     * @param $currentStep
     * @return mixed|void
     */
    public function CheckBeforeSubmitOrApprove($dataId, $currentStep)
    {
        // TODO: Implement CheckBeforeSubmitOrApprove() method.
        $mod = self::get($dataId);
        $options = unserialize($mod['form_data']);
        $res = "";
        foreach ($options as $item) {
            if ($item['Step'] == $currentStep && (!empty($item['Required'])) && empty($item['Value'])) {
                $res .= $item['Required'] . " ";
            }
        }
        return trim($res, ",");
    }

    /**
     * 更新审批信息
     * @param $dataId
     * @param $currentApproveId
     * @param $currentStep
     * @param $approveStatus
     * @return mixed|void
     */
    public function UpdateApproveInfo($dataId, $currentApproveId, $currentStep, $approveStatus)
    {
        self::save([
            'CurrentApproverId' => $currentApproveId,
            'CurrentStep' => $currentStep,
            'ApproveStatus' => $approveStatus
        ]);
    }

}