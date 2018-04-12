<?php
/**
 * Created by PhpStorm.
 * User: zhifang
 * Date: 2018/4/8
 * Time: 15:30
 */

namespace app\standard\model;

use think\Model;

class ControlPoint extends Model
{
    protected $name = 'controlpoint';

    /**
     * 获取节点下所有子节点
     * @param $id
     * @return array
     * @throws \think\exception\DbException
     */
    public function getChilds($id)
    {
        $list = MaterialTrackingDivision::all();
        if ($list) {
            return $this->_getChilds($list, $id);
        }
    }

    function _getChilds($list, $id)
    {
        $nodeArray = array();
        foreach ($list as $item) {
            if ($item['pid'] == $id) {
                $nodeArray[] = $item['id'];
                $nodeArray = array_merge($nodeArray, $this->_getChilds($list, $item['id']));
            }
        }
        return $nodeArray;
    }

    /**
     * 执行状态输出器
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getisexecutedTxtAttr($value, $data)
    {
        $status = [1 => '已执行', 2 => '未执行'];
        return $status[$data['isexecuted']];
    }
}