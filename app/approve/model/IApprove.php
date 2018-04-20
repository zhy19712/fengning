<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/4/20
 * Time: 10:55
 */
namespace  app\approve\model;
/**
 * 涉及审批的业务需要实现该接口
 * 用以处理审批相关依赖逻辑
 * Interface IApprove
 * @package app\approve\model
 */
interface IApprove
{
    /**
     * 提交审批更新业务状态
     * @param $approverIds 审批人id串
     * @param $currentApproverId 当前审批人Id
     * @param int $currentStep 当前审批步骤，创建审批时为1
     * @param int $approveStatus 审批状态：0新建；1审批中；2已审批；-1被退回
     * @return mixed
     */
    public function SubmitHandle($dataId,$approverIds,$currentApproverId,$currentStep=1,$approveStatus=1);
}