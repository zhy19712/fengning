<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/4/19
 * Time: 16:55
 */

namespace app\approve\controller;

use app\admin\controller\Permissions;
use app\approve\model\ApproveModel;
use app\quality\model\QualityFormInfoModel;
use think\Request;
use think\Session;

/**
 * 流程审批
 * Class Approve
 * @package app\approve\controller
 */
class Approve extends Permissions
{
    protected $approveService;

    public function __construct(Request $request = null)
    {
        $this->approveService = new ApproveModel();
        parent::__construct($request);
    }

    /**
     * 提交审批
     * @param $dataId
     * @param $dataType
     * @return mixed
     */
    public function submit($dataId, $dataType, $referFlow = null)
    {
        if ($this->request->isAjax()) {
            $par = input('post.');
            $res = $this->approveService->submit($par['dataId'], new $par['dataType'], Session::get('current_id'), $par['approveids']);
            return json(['code' => $res]);
        }
        $this->assign("dataId", $dataId);
        $this->assign("dataType", $dataType);
        $this->assign("referFolw", $referFlow);
        return $this->fetch();
    }

    /**
     * 选择人员
     * @return mixed
     */
    public function selectMumber($dataType = null)
    {
        $this->assign('dataType', $dataType);
        return $this->fetch();
    }

    /**
     * 获取常用审批人
     * @param $dataType 带有命名空间的业务模型
     * @return \think\response\Json
     */
    public function FrequentlyUsedApprover($dataType)
    {
        //QualityFormInfoModel::
       $userlist= $this->approveService->FrequentlyUsedApprover(new $dataType);
       return json($userlist);
    }
}