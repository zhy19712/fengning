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

class LabelSnapshotModel extends Model
{
    protected $name = 'quality_label_snapshot';
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

    public function getLabelSnapshotTb($type,$picture_id)
    {
        $data = $this->where(['type'=>$type,'picture_id'=>$picture_id])->column('label_snapshot');
        return ['code'=>1,'data'=>$data,'msg'=>'图片的base64值'];
    }


}