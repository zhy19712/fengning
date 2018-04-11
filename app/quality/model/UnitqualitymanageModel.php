<?php
/**
 * Created by PhpStorm.
 * User: sir
 * Date: 2018/4/11
 * Time: 14:19
 */

namespace app\quality\model;


use think\Db;
use think\Model;

class UnitqualitymanageModel extends Model
{
    /**
     * 获取工序
     * @param $id
     * @return array
     * @author hutao
     */
    public function getProductionProcessesInfo($id)
    {
        $division = new DivisionModel();
        $idArr = $division->cateTree($id); // 递归获取当前节点的所有子节点
        array_push($idArr,$id); // 当前节点编号 和 所以子节点编号 数组
        $en_type_arr = $division->getEnTypeById($idArr); // 获取当前节点 的 包含 子节点 的 所有 工程类型
        $unit = new DivisionUnitModel();
        $unit_en_type_arr = $unit->getEnTypeArr($idArr); // 获取当前节点 和子节点  所包含的 所有 单元工程段号(单元划分) 的 所有 工程类型
        $arr = array_merge($en_type_arr,$unit_en_type_arr); // 合并所得工程类型
        $new_arr = array_unique($arr); // 删除重复工程类型
        // 根据所得工程类型 获取 包含的 工序
        $data = Db::name('materialtrackingdivision')->whereIn('pid',$new_arr)->column('id,name');
        return $data;
    }

}