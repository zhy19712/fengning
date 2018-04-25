<?php
/**
 * Created by PhpStorm.
 * User: sir
 * Date: 2018/4/18
 * Time: 17:30
 */

namespace app\quality\model;


use think\Db;
use think\exception\PDOException;
use think\Model;

class CustomAttributeModel extends Model
{
    protected $name = 'quality_custom_attribute';
    //自动写入创建、更新时间 insertGetId和update方法中无效，只能用于save方法
    protected $autoWriteTimestamp = true;

    public function insertTb($param)
    {
        try {
            $result = $this->allowField(true)->save($param);
            if (false === $result) {
                return ['code' => -1, 'msg' => $this->getError()];
            } else {
                return ['code' => 1, 'msg' => '添加成功'];
            }
        } catch (PDOException $e) {
            return ['code' => -1, 'msg' => $e->getMessage()];
        }
    }

    public function getAttrTb($picture_id)
    {
        $attr = $this->where(['picture_id'=>$picture_id])->column('attr_name as attrKey,attr_value as attrVal');
        return ['code'=>1,'attr'=>$attr,'模型图自定义属性'];
    }


}