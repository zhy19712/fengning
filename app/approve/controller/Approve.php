<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/4/19
 * Time: 16:55
 */
namespace  app\approve\controller;
use app\admin\controller\Permissions;

/**
 * 流程审批
 * Class Approve
 * @package app\approve\controller
 */
class Approve extends Permissions
{
    /**
     * 提交审批
     * @param $dataId
     * @param $dataType
     * @return mixed
     */
    public function submit($dataId, $dataType)
    {
        return $this->fetch();
    }
}