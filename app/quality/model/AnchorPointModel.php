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

class AnchorPointModel extends Model
{
    protected $name = 'quality_anchor_point';
    //自动写入创建、更新时间 insertGetId和update方法中无效，只能用于save方法
    protected $autoWriteTimestamp = true;

    public function insertTb($param)
    {
        try {
            $result = $this->allowField(true)->save($param);
            $last_insert_id = $this->getLastInsID();
            if (false === $result) {
                return ['code' => -1, 'msg' => $this->getError()];
            } else {
                return ['code' => 1,'anchor_point_id'=>$last_insert_id, 'msg' => '添加成功'];
            }
        } catch (PDOException $e) {
            return ['code' => -1, 'msg' => $e->getMessage()];
        }
    }

    public function editTb($param)
    {
        try {
            $result = $this->allowField(true)->save($param, ['id' => $param['id']]);
            if (false === $result) {
                return ['code' => -1, 'msg' => $this->getError()];
            } else {
                return ['code' => 1, 'msg' => '上传成功'];
            }
        } catch (PDOException $e) {
            return ['code' => 0, 'msg' => $e->getMessage()];
        }
    }

    public function deleteTb($id)
    {
        try {
            $this->where('id', $id)->delete();
            return ['code' => 1, 'msg' => '删除成功'];
        } catch (PDOException $e) {
            return ['code' => -1, 'msg' => $e->getMessage()];
        }
    }

    public function getAnchorTb($name='')
    {
        if($name){
            $data = Db::name('quality_anchor_point')
                ->where(['picture_type'=>1,'anchor_name'=>$name])
                ->field('id as anchor_point_id,picture_number,anchor_name,component_name,user_name,coordinate_x,coordinate_y,coordinate_z,remark,attachment_id')
                ->select();
        }else{
            $data = Db::name('quality_anchor_point')
                ->where(['picture_type'=>1])
                ->field('id as anchor_point_id,picture_number,anchor_name,component_name,user_name,coordinate_x,coordinate_y,coordinate_z,remark,attachment_id')
                ->select();
        }
        return $data;
    }

}