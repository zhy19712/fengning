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
                // 上传成功后，返回文件主键和地址
                $data = Db::name('attachment')->where('id',$param['attachment_id'])->column('id as attachment_id,filepath');
                return ['code' => 1,'data'=>$data, 'msg' => '上传成功'];
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
            $data = Db::name('quality_anchor_point')->alias('p')
                ->join('attachment a','p.attachment_id = a.id','left')
                ->where(['p.picture_type'=>1,'p.anchor_name'=>$name])
                ->field('p.id as anchor_point_id,p.picture_number,p.anchor_name,p.component_name,p.user_name,p.coordinate_x,p.coordinate_y,p.coordinate_z,p.remark,p.attachment_id,a.filepath')
                ->select();
        }else{
            $data = Db::name('quality_anchor_point')->alias('p')
                ->join('attachment a','p.attachment_id = a.id','left')
                ->where(['p.picture_type'=>1])
                ->field('p.id as anchor_point_id,p.picture_number,p.anchor_name,p.component_name,p.user_name,p.coordinate_x,p.coordinate_y,p.coordinate_z,p.remark,p.attachment_id,a.filepath')
                ->select();
        }
        return $data;
    }

}