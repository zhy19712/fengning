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

class ApproveModel extends Model
{
    protected $name = 'approve';
    protected $autoWriteTimestamp = true;

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
}