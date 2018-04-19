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

    public function getAllNumber($add_id)
    {
        $division_id = Db::name('quality_unit')->where('division_id',$add_id)->column('id');
        $picture_id = $this->where(['division_id'=>['in',$division_id]])->column('picture_id,picture_name');
        return $picture_id;
    }

    public function getModelPicture($id)
    {
        $picture_id = $this->where(['division_id'=>['eq',$id]])->value('picture_id');
        return $picture_id;
    }


}