<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/4/20
 * Time: 10:06
 */

namespace app\approve\model;

use app\admin\model\Admin;
use think\Exception;
use think\Model;
use think\Session;

class ApproveModel extends Model
{
    protected $name = 'approve';
    protected $autoWriteTimestamp = true;

    /**
     * 审批人
     * @return \think\model\relation\HasOne
     */
    public function User()
    {
        return $this->hasOne('app\admin\Admin', 'id', 'user_id');
    }

    /**
     * 提交审批
     * @param $dataId 业务id
     * @param $dataType 业务类型
     * @param $userId 提交人id
     * @param $approveIds 审批人列表Id串，“,”分割
     */
    public function submit($dataId, IApprove $dataType, $userId, $approveIds)
    {
        try {
            $mod = array();
            $mod['data_id'] = $dataId;
            $mod['data_type'] = $dataType;
            $mod['user_id'] = $userId;
            $mod['result'] = "提交";
            $mod['mark'] = "提交审批";
            $this->save($mod);
            $dataType->SubmitHandle($dataId, $approveIds, $userId);
            return 1;
        } catch (Exception $exception) {
            return -1;
        }
    }

    public function getApproveInfo($dataId, IApprove $dataType)
    {
        //得到业务基本信息  user_id approverIds,CurrentApproverId,CurrentStep
        $info = $dataType->GetApproveInfo($dataId);
        if (empty($info)) {
            return -1;
        }
        $mod = new ApproveInfo();
        $approveIds = explode(',', $info['ApproveIds']);
        //流程结尾判断
        if ($info['CurrentStep'] < sizeof($approveIds)) {
            $mod->NextApproverId = $approveIds[$info['CurrentStep']];
            $mod->NextApproverName = Admin::get($mod->NextApproverId)['nickname'];
        }
        $mod->PreApproverId = $info['user_id'];
        $mod->PreApproverName = Admin::get($info['user_id'])['nickname'];
        return $mod;
    }

    /**
     * 获取业务下常用审批人
     * @param IApprove $dataType 业务Model对象
     * @return \think\response\Json
     */
    public function FrequentlyUsedApprover(IApprove $dataType)
    {
        $userlist = $dataType->FrequentlyUsedApprover(Session::get('current_id'));
        return $userlist;
    }

    /**
     * 业务数据完整性检测
     * @param $dataId
     * @param IApprove $dataType
     * @param $currentStep
     * @return mixed
     */
    public function CheckBeforeSubmitOrApprove($dataId, IApprove $dataType, $currentStep)
    {
        return $dataType->CheckBeforeSubmitOrApprove($dataId,$currentStep);
    }
}

class ApproveInfo
{
    //当前审批人
    public $CurrentApproverId = "";
    public $CurrentApproverName = "";
    //审批时间
    public $ApproveTime;
    //下一步审批人
    public $NextApproverId = "";
    public $NextApproverName = "审批完成";
    //上一步审批人
    public $PreApproverId = "";
    public $PreApproverName = "";
    //审批人ID串
    public $ApproverIds = "";
    //创建人
    public $CreateUserId = "";
    //当前步骤
    public $CurrentStep = "";
}