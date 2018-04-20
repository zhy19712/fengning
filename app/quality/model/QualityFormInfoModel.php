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
use think\Model;

class QualityFormInfoModel extends Model implements IApprove
{
    protected $name = 'quality_form_info';
    protected $autoWriteTimestamp = true;

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
        $userlist = self::where(['user_id' => $user_id])->whereNotNull(['ApproveIds'], 'and')->field('ApproveIds')->select();
        $ids=array();
        foreach ($userlist as $item) {
            $_ids = explode(",", $item);
            foreach ($_ids as $_id)
            {
                if (!in_array($_id,$ids))
                {
                    $ids[]=$_id;
                }
            }
        }
        $adminService=new Admin();
        $users=$adminService->whereIn('id',$ids)->column('id,nickname');
        return $users;
    }
}