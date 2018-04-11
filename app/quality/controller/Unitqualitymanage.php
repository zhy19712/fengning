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
// 质量管理

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
        if(request()->isAjax()){
            $node = new DivisionModel();
            $nodeStr = $node->getNodeInfo(2); // 只取到子单位工程
            return json($nodeStr);
        }
        if($type==1){
            return $this->fetch();
        }
        return $this->fetch('control');
    }

}