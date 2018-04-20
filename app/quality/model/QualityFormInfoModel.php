<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/4/18
 * Time: 13:48
 */
namespace app\quality\model;
use app\approve\model\IApprove;
use think\Model;

class QualityFormInfoModel extends Model implements IApprove
{
    protected $name='quality_form_info';
    protected $autoWriteTimestamp=true;

    public function SubmitHandle($dataId,$approverIds, $currentApproverId, $currentStep = 1, $approveStatus = 1)
    {
        $this->save(['ApproveIds'=>$approverIds,'CurrentApproverId'=>$currentApproverId,'CurrentStep'=>$currentStep,'ApproveStatus'=>$approveStatus],['id'=>$dataId]);
    }
}