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
    protected $name='controlpoint';
    /**
     * 获取节点下所有子节点
     * @param $id
     * @return array
     * @throws \think\exception\DbException
     */
    public function getChilds($id)
    {
        $list=ControlPoint::all();
        if ($list)
        {
            return $this->_getChilds($list,$id);
        }
    }
}