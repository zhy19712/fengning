<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/4/20
 * Time: 10:06
 */

namespace app\approve\model;

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
        return $this->hasOne('app\admin\Admin','id','user_id');
    }
    /**
     * 提交审批
     * @param $dataId 业务id
     * @param $dataType 业务类型
     * @param $userId 提交人id
     * @param $approveIds 审批人列表Id串，“,”分割
     */
    public function submit($dataId,IApprove $dataType, $userId, $approveIds)
    {
        try {
            $mod = array();
            $mod['data_id'] = $dataId;
            $mod['data_type'] = $dataType;
            $mod['user_id'] = $userId;
            $mod['result'] = "提交";
            $mod['mark'] = "提交审批";
            $this->save($mod);
            $dataType->SubmitHandle($dataId,$approveIds,$userId);
            return 1;
        } catch (Exception $exception) {
            return -1;
        }
    }

    /**
     * 获取业务下常用审批人
     * @param IApprove $dataType 业务Model对象
     * @return \think\response\Json
     */
    public function FrequentlyUsedApprover(IApprove $dataType)
    {
        $userlist=$dataType->FrequentlyUsedApprover(Session::get('current_id'));
        return json($userlist);
    }
}