<?php
/**
 * Created by PhpStorm.
 * User: sir
 * Date: 2018/4/18
 * Time: 17:30
 */

namespace app\quality\model;


use think\exception\PDOException;
use think\Model;

class PictureModel extends Model
{
    protected $name = 'quality_model_picture';
    //自动写入创建、更新时间 insertGetId和update方法中无效，只能用于save方法
    protected $autoWriteTimestamp = true;

    public function insertTb($param)
    {
        try {
            $result = $this->allowField(true)->save($param);
            if (false === $result) {
                return ['code' => -1, 'msg' => $this->getError()];
            } else {
                return ['code' => 1, 'msg' => '关联成功'];
            }
        } catch (PDOException $e) {
            return ['code' => -1, 'msg' => $e->getMessage()];
        }
    }

    public function getAllName()
    {
        // picture_type  1工程划分模型 2 建筑模型 3三D模型
        $data = $this->where('picture_type',1)->field('id as picture_id,picture_number,picture_name')->select();
        return $data;
    }

}