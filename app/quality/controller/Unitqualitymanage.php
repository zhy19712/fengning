<?php
/**
 * Created by PhpStorm.
 * User: sir
 * Date: 2018/4/11
 * Time: 14:17
 */

namespace app\quality\controller;


use app\admin\controller\Permissions;
use app\quality\model\DivisionModel;
use app\quality\model\UnitqualitymanageModel;

// 单位质量管理

class Unitqualitymanage extends Permissions
{
    /**
     * 单位策划 或者 单位管控 初始化左侧树节点
     * @param int $type
     * @return mixed|\think\response\Json
     * @author hutao
     */
    public function index($type = 1)
    {
        if($this->request->isAjax()){
            $node = new DivisionModel();
            $nodeStr = $node->getNodeInfo(2); // 2 只取到子单位工程
            return json($nodeStr);
        }
        if($type==1){
            return $this->fetch();
        }
        return $this->fetch('control');
    }

    /**
     * 获取节点工序
     * @return \think\response\Json
     * @author hutao
     */
    public function unitPlanning()
    {
        $param = input('param.');
        $id = isset($param['add_id']) ? $param['add_id'] : 0;
        if($this->request->isAjax() && $id){
            $u = new UnitqualitymanageModel();
            $data = $u->getProductionProcessesInfo($id); // 获取工序信息
            return json(['code' => 1,'data' => $data,'msg' => '工序']);
        }else{
            return json(['code' => -1,'msg' => '编号有误']);
        }
    }

}